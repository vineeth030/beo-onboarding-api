<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $emailDomain = substr(strrchr($request->email, '@'), 1);

        if ($emailDomain == 'beo.in' || empty($emailDomain)) {

            $result = $this->adminAuth($request->input('email'), $request->input('password'));
        } else {

            $result = $this->employeeAuth($request->input('email'), $request->input('password'));
        }

        return response()->json($result['data'], $result['status']);
    }

    private function employeeAuth($email, $password): array
    {

        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return [
                'data' => ['message' => 'The provided credentials are invalid','token' => null],
                'status' => 401
            ];
        }

        // Check if the candidate has at least one active offer
        if ($user->employee) {
            $hasActiveOffer = $user->employee->offers()->where('is_revoked', false)->exists();

            if (! $hasActiveOffer) {
                return [
                    'data' => ['message' => 'No active offers found, please contact the HR team','token' => null],
                    'status' => 403
                ];
            }
        }

        $token = $user->createToken($email)->plainTextToken;

        if ($user->role != 'superadmin') {
            Activity::create([
                'employee_id' => $user->employee?->id,
                'performed_by_user_id' => $user->id,
                'user_type' => 'candidate',
                'type' => 'candidate.login',
                'title' => $user->employee?->full_name.' logged in successfully.',
            ]);
        }

        return [
            'message' => 'Login successful',
            'token' => $token,
            'role' => $user->role,
            'user_id' => $user->id,
            'employee_id' => $user->employee?->id,
        ];
        return [
            'data' => [
                'message' => 'Login successful',
                'token' => $token,
                'role' => $user->role,
                'user_id' => $user->id,
                'employee_id' => $user->employee?->id
            ],
            'status' => 200
        ];
    }

    private function adminAuth($userName, $password): array
    {
        try {
            [$sessionToken, $userIdCode, $message, $status] = (new BEOSystemController)->login($userName, $password);
        } catch (\Throwable $th) {
            return [
                'data' => ['message' => $th->getMessage(),'sessionToken' => null],
                'status' => 502
            ];
        }

        if ($sessionToken == null) {
            return [
                'data' => ['message' => $message,'sessionToken' => null],
                'status' => 401,
            ];
        }

        try {
            $adminDetails = (new BEOSystemController)->retrive($sessionToken, $userIdCode);
        } catch (\Throwable $th) {
            return [
                'data' => ['message' => $th->getMessage(),'sessionToken' => null],
                'status' => 502,
            ];
        }

        if ($adminDetails['group'] != 'Human Resources') {
            return [
                'data' => ['message' => 'Unauthorised access.','sessionToken' => null],
                'status' => 403,
            ];
        }

        // Update users table with this api response.
        $user = User::firstOrCreate(
            ['email' => $adminDetails['email']], // search condition
            ['name' => $adminDetails['name'], 'password' => Hash::make($password), 'role' => 'admin'] // values if not found
        );

        $token = $user->createToken($adminDetails['email'])->plainTextToken;

        return [
            'data' => [
                'message' => $adminDetails['message'],
                'sessionToken' => $sessionToken,
                'token' => $token,
                'role' => 'admin',
                'user_id' => $user->id,
                'employee_id' => 0,
                'userIdCode' => $userIdCode,
            ],
            'status' => 200
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
