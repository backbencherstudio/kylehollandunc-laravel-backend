<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\CommonTrait;
use App\Notifications\SendPasswordOtp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PassowordResetController extends Controller
{
    use CommonTrait;

    public function requestOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');

        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(5);

        // Store the OTP in the database with an expiration time (e.g., 5 minutes)
        \DB::table('password_reset_otps')->updateOrInsert(
            ['email' => $email],
            [
                'otp' => $otp,
                'expires_at' => $expiresAt,
                'updated_at' => now(),
            ]
        );

        // Send the OTP via email
        $user = new User();
        $user->email = $email;
        $user->notify(new SendPasswordOtp($otp));

        return $this->sendResponse(['message' => 'OTP sent to your email.'], 200);
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        $email = $request->input('email');
        $otp = $request->input('otp');

        // Retrieve the OTP record from the database
        $record = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('otp', $otp)
            ->first();

        // dd($record);
        if (!$record) {
            return $this->sendError('Invalid OTP.', [], 400);
        }

        // Check if the OTP has expired
        if (Carbon::now()->greaterThan(Carbon::parse($record->expires_at))) {
            return $this->sendError('OTP has expired.', [], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }

        // $user->update(['password' => Hash::make($request->password)]);

        // Delete OTP after use
        DB::table('password_reset_otps')->where('email', $request->email)->delete();

        return $this->sendResponse(['message' => 'OTP verified successfully.'], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|min:8|same:new_password',
        ]);

        if ($validator->fails()) {
            return $this->sendError(['Validation Error.', $validator->errors()], 422);
        }

        // dd($request->all());
        $user = User::where('email', $request->email)->first();
        // dd($user);
        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }

        $user->update(['password' => Hash::make($request->confirm_password)]);

        return $this->sendResponse(['message' => 'Password reset successfully.'], 200);
    }
}
