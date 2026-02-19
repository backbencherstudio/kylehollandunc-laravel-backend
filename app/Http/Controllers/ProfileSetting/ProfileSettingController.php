<?php

namespace App\Http\Controllers\ProfileSetting;

use App\Http\Controllers\Controller;
use App\Models\ProfileSetting\ProfileSetting;
use App\Models\User;
use App\Traits\CommonTrait;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileSettingController extends Controller
{
    use CommonTrait;

    public function index()
    {
        // get profile settings for the authenticated user
    }

    public function profileUpdate(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
            ]);

            if ($validate->fails()) {
                return $this->sendError($validate->errors());
            }

            $user = Auth()->user();

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            ProfileSetting::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => $request->phone,
                    'country' => $request->country,
                    'state' => $request->state,
                ]
            );
            // dd($user->id);

            // This method can be used to update profile settings if needed
            return $this->sendResponse(['message' => 'Profile settings updated successfully.']);
        } catch (\Exception $e) {
            return $this->sendError('Failed to update profile settings.', ['error' => $e->getMessage()]);
        }
    }
}
