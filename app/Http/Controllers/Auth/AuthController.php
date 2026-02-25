<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting\Setting;
use App\Models\User;
use App\Notifications\AdminNotify;
use App\Traits\CommonTrait;
use App\Notifications\SendPasswordOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    use CommonTrait;

    public function getUser(Request $request)
    {
        try {
            $user = $request->user();
            $user->load('profileInfo');
            return $this->sendResponse($user, 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function getAllUsers()
    {
        try {
            $users = User::with('profileInfo')->get();
            return $this->sendResponse($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function register(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            if (User::where('email', $request->email)->exists()) {
                return $this->sendError('Email already exists', 400);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // dd(Setting::get('user_registration', '1'));
            if (Setting::get('user_registration', '1')) {
                $adminEmail = env('ADMIN_EMAIL');
                if ($adminEmail) {
                    $subject = 'New User Registration';
                    $data = [
                        'name' => $request->name,
                        'email' => $request->email,
                    ];
                    Notification::route('mail', $adminEmail)->notify(new AdminNotify($subject, $data));
                }
            }

            return $this->sendResponse($user, 'User registered successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {
        // return $this->sendResponse($request->all(), 'Request data retrieved');
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->sendError('Invalid credentials', 401);
            }

            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;
            $user->token = $token;

            return $this->sendResponse($user, 'User logged in successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->sendResponse(null, 'User logged out successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return $this->sendResponse(null, 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
