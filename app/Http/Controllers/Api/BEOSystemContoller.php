<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class BEOSystemContoller extends Controller
{
    private string $aesKey;
    private string $aesIV;
    private string $firebaseId;
    private const BEO_SYSTEM_LOGIN_SUCCESS_CODE = 104;
    private const BEO_SYSTEM_INVALID_CREDENTIALS_CODE = 102;
    private const BEO_SYSTEM_LOGIN_API_URL = '/beosystem/api/Login/UserLoginForMobApp?deviceId=9e528a0c-2302-4474-b5be-8bf829b30e5a';
    private const BEO_SYSTEM_USER_DETAILS_API_URL = '/beosystem/api/Login/UserInfoForMobApp';
    private const BEO_SYSTEM_CREATE_USER_API_URL = '/beosystem/api/Users/SaveUserDetailsForMobApp';

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
    public function store($sessionToken)
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
            //
        ];
    } 
}
