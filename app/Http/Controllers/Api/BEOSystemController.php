<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\BeoEmployee;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BEOSystemController extends Controller
{
    private string $aesKey;
    private string $aesIV;
    private string $firebaseId;
    private const BEO_SYSTEM_LOGIN_SUCCESS_CODE = 104;
    private const BEO_SYSTEM_INVALID_CREDENTIALS_CODE = 102;
    private const BEO_SYSTEM_LOGIN_API_URL = '/beosystem/api/Login/UserLoginForMobApp?deviceId=9e528a0c-2302-4474-b5be-8bf829b30e5a';
    private const BEO_SYSTEM_USER_DETAILS_API_URL = '/beosystem/api/Login/UserInfoForMobApp';
    private const BEO_SYSTEM_CREATE_USER_API_URL = '/beosystem/api/Users/SaveUserDetailsForMobApp';
    private const BEO_SYSTEM_COUNTRIES_API_URL = '/beosystem/api/Users/GetCountryListForMobApp';
    private const BEO_SYSTEM_STATES_API_URL = '/beosystem/api/Users/GetStateListForMobApp';
    private const BEO_SYSTEM_DESIGNATIONS_API_URL = '/beosystem/api/Users/NeccessaryUsersDetailsInfoForMobApp';
    private const BEO_SYSTEM_EMPLOYEE_DETAILS_API_URL = '/beosystem/api/Users/GetGroupListWithEmployessInfoForMobApp';

    public function __construct()
    {
        $this->aesKey = config('beosystem.aes_key');
        $this->aesIV  = config('beosystem.aes_iv');
        $this->firebaseId  = config('beosystem.firebase_id');
    }

    /**
     * Login an employee.
     */
    public function login( string $userName, string $password ): array
    {
        $requestBody = $this->prepareLoginPayload($userName, $password);

        $response = Http::post(config('beosystem.base_url') . self::BEO_SYSTEM_LOGIN_API_URL, $requestBody)->throw();

        if ($response->failed()) {
            return [null, 0, 'BEO system unavailable. Please try again later.'];
        }

        $data = $response->json();

        if (($data['status'] ?? null) == self::BEO_SYSTEM_INVALID_CREDENTIALS_CODE) {
            return [null,'Invalid Credentials.'];
        }

        return [ $data['sessionToken'], $data['userIdCode'], 'Login success.' ];
    }

    public function countries() : array {

        return Cache::remember('beo_countries', now()->addMonths(3), function () {

            $response = Http::get(
                config('beosystem.base_url') . self::BEO_SYSTEM_COUNTRIES_API_URL
            );

            if ($response->failed()) {
                return [null, 'BEO system unavailable. Please try again later.'];
            }

            return $response->json('con_list') ?? [];
        });
    }

    public function states() : array {

        return Cache::remember('beo_states', now()->addMonths(3), function () {

            $response = Http::get(config('beosystem.base_url') . self::BEO_SYSTEM_STATES_API_URL)->throw();

            if ($response->failed()) {
                return [null, 'BEO system unavailable. Please try again later.'];
            }

            return $response->json('sta_list');
        });
    }

    public function designations(string $sessionToken, int $userIdCode) : array {

        return Cache::remember('beo_designations', now()->addMonths(1), function ($sessionToken, $userIdCode) {

            $response = Http::withOptions(['query' => ['sessionToken' => $sessionToken]])
                            ->post(
                                config('beosystem.base_url') . self::BEO_SYSTEM_DESIGNATIONS_API_URL,
                                ['userIdCode' => $userIdCode]
                            )->throw();

            if ($response->failed()) {
                return [null, 'BEO system unavailable. Please try again later.'];
            }

            return $response->json('AUserNeccesaryDesigList_lists');
        });
    }

    /**
     * Return the specified employee.
     */
    public function retrive(string $sessionToken, int $userIdCode): array
    {
        $response = Http::withOptions(['query' => ['sessionToken' => $sessionToken]])
                        ->post(
                            config('beosystem.base_url') . self::BEO_SYSTEM_USER_DETAILS_API_URL, 
                            ['userIdCode' => $userIdCode]
                        )->throw();

        if ($response->failed()) {
            return [null, 'BEO system unavailable. Please try again later.'];
        }

        $data = $response->json();

        if (($data['status'] ?? null) != 200) {
            return [null,'Internal Server Error'];
        }

        return [ 
            'email' => $data['email'], 
            'name' => $data['firstName'] . ' ' . $data['lastName'], 
            'designation' => $data['designation'], 
            'group' => trim($data['sGroupName']), 
            'assessmentView' => $data['assessmentView'], 
            'message' => 'Success.' 
        ];
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store($sessionToken): array
    {
        $requestBody = $this->prepareStoreUserPayload();

        $response = Http::withOptions(['query' => ['sessionToken' => $sessionToken]])
                        ->post(config('beosystem.base_url') . self::BEO_SYSTEM_CREATE_USER_API_URL, $requestBody)->throw();

        if ($response->failed()) {
            return [null, 'BEO system unavailable. Please try again later.'];
        }

        $data = $response->json();

        if (($data['status'] ?? null) != 200) {
            return [null,'Internal Server Error'];
        }

        return [
            'message' => 'New employee created successfully.' 
        ];
    }

    /**
     * Store BEO employees data to onboarding app database.
     * 
     * Seed beo_employees table with this data evey time admin hit refresh data.
     */
    public function storeBEOEmployeesToOnboarding(Request $request) : array {

        $response = Http::withOptions(['query' => ['sessionToken' => $request->get('sessionToken'), 'userIdCode' => $request->get('userIdCode')]])
                        ->post( config('beosystem.base_url') . self::BEO_SYSTEM_EMPLOYEE_DETAILS_API_URL )->throw();

        if ($response->failed()) {
            return [null, 'BEO system unavailable. Please try again later.'];
        }

        $data = json_decode($response, true);

        if ($data['status'] == 120) {
            return ['message' => 'BEO System session expired', 'code' => 120];
        }

        DB::table('beo_employees')->truncate();

        // Loop through groups and employees
        foreach ($data['groupList'] as $group) {
            foreach ($group['employeeLists'] as $employee) {
                 BeoEmployee::create([
                    'name' => $employee['employeeName'],
                    'employee_id' => $employee['employeeId'],
                    'photo_path' => $employee['imageUrl'],
                    'designation' => $employee['designation'],
                    'phone' => $employee['mobileNumber'],
                    'email' => $employee['email']
                ]);
            }
        }

        // When BEO System API session is expired, make the onboarding app logout.
        
        return ['message' => 'success', 'code' => 200];
    }

    public function getBEOEmployees() : Collection {

        return BeoEmployee::all();
    }

    public function getSingleBEOEmployee($employee_id) : BeoEmployee {
        
        return BeoEmployee::where('employee_id', $employee_id)->first();
    }

    private function prepareLoginPayload($userName, $password){

        $encryptedUserName = openssl_encrypt($userName, 'AES-256-CBC', $this->aesKey, 0, $this->aesIV);
        $encryptedPassword = openssl_encrypt($password, 'AES-256-CBC', $this->aesKey, 0, $this->aesIV);

        return [
            'accessId' => $encryptedUserName,
            'blockId' => $encryptedPassword,
            'firbaseId' => $this->firebaseId
        ];
    }
    
    private function prepareStoreUserPayload(){

        return [
            "userIdCode"=> 198,
            "editUid" => 0, // Set to zero when creating an employee.
            "userID"=> "asdasdasd.a",
            "firstName"=> "abcd",
            "lastName"=> "a",
            "fatherName"=> "a",
            "nationality"=> 100, // BEO Country API
            "communAddressLine1"=> "communAddressLine1",
            "communAddressLine2"=> "communAddressLine2",
            "communAddDistrict"=> "communAddDistrict",
            "communAddPinCode"=> "communAddPinCode",
            "communAddstate"=> "communAddstate", // BEO State API
            "communAddcountry"=> 101, // BEO Country API
            "mobile"=> "987654321",
            "landLine"=> "123456",
            "permntAddSameAsCommun"=> 0,
            "permntAddressLine1"=> "permntAddressLine1",
            "permntAddressLine2"=> "permntAddressLine2",
            "permntAddDistrict"=> "permntAddDistrict",
            "permntAddpinCode"=> "permntAddpinCode",
            "permntAddstate"=> "Kerala",
            "permntAddcountry"=> 102, 
            "emailId"=> "d.ara@asdasd.mmm",
            "BEOChat"=> "d.ara@fff.mmm", // Can be empty.
            "fax"=> "12345678", // Can be empty
            "password"=> "a",
            "retypePassword"=> "a",
            "prfLang"=> "en-GB",
            "empId"=> 732, // To be entered manually.
            "dob"=> "",
            "gender"=> "F",
            "designation"=> 0, // BEO API
            "group"=> 3, // BEO API
            "grade"=> 0,
            "doj"=> "",
            "noticePeriodStart"=> "",
            "noticePeriodEnd"=> "",
            "relievingDate"=> "",
            "floorId"=> 0,
            "chkHalfDay"=> false,
            "chkHour"=> false,
            "timeType"=> 0,
            "bloodGroupId"=> 6,
            "bloodGroup"=> "O-",
            "tshirtSize"=> "S",
            "weeklyWorkingHour"=> "",
            "assessmentType"=> 0,
            "month"=> 0,
            "specialTypeUser"=> 0,
            "sType"=> "Edit",
            "permntWfh"=> 0
        ];
    } 
}
