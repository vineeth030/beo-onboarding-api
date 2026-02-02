<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

            $authResponse = $this->adminAuth($request->input('email'), $request->input('password'));
        } else {

            $authResponse = $this->employeeAuth($request->input('email'), $request->input('password'));
        }

        return response()->json($authResponse);
    }

    private function employeeAuth($email, $password): array
    {

        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are not correct.'],
            ]);
        }

        // Check if the candidate has at least one active offer
        if ($user->employee) {
            $hasActiveOffer = $user->employee->offers()->where('is_revoked', false)->exists();

            if (! $hasActiveOffer) {
                abort(403, 'No active offers found, please contact the HR team');
            }
        }

        $token = $user->createToken($email)->plainTextToken;

        if ($user->role != 'superadmin') {
            Activity::create([
                'employee_id' => $user->employee?->id,
                'performed_by_user_id' => $user->id,
                'user_type' => 'candidate',
                'type' => 'candidate.login',
                'title' => $user->employee?->first_name.' '.$user->employee?->last_name.' logged in successfully.',
            ]);
        }

        return [
            'message' => 'Login successful',
            'token' => $token,
            'role' => $user->role,
            'user_id' => $user->id,
            'employee_id' => $user->employee?->id,
        ];
    }

    private function adminAuth($userName, $password): array
    {
        try {
            [$sessionToken, $userIdCode, $message] = (new BEOSystemController)->login($userName, $password);
        } catch (\Throwable $th) {
            return [
                'message' => $th->getMessage(),
                'sessionToken' => null,
            ];
        }

        if ($sessionToken == null) {
            return [
                'message' => $message,
                'sessionToken' => null,
            ];
        }

        try {
            $adminDetails = (new BEOSystemController)->retrive($sessionToken, $userIdCode);
        } catch (\Throwable $th) {
            return [
                'message' => $th->getMessage(),
                'sessionToken' => null,
            ];
        }

        if ($adminDetails['group'] != 'Human Resources') {
            return [
                'message' => 'Unauthorised access.',
                'sessionToken' => null,
            ];
        }

        // Update users table with this api response.
        $user = User::firstOrCreate(
            ['email' => $adminDetails['email']], // search condition
            ['name' => $adminDetails['name'], 'password' => Hash::make($password), 'role' => 'admin'] // values if not found
        );

        $token = $user->createToken($adminDetails['email'])->plainTextToken;

        return [
            'message' => $adminDetails['message'],
            'sessionToken' => $sessionToken,
            'token' => $token,
            'role' => 'admin',
            'user_id' => $user->id,
            'employee_id' => 0,
            'userIdCode' => $userIdCode,
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
