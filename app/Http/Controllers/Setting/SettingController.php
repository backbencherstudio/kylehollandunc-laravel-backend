<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\Setting;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    use CommonTrait;

    public function index()
    {
        try {
            $notificationSettings = [
                'user_registration' => Setting::get('user_registration', '0'),
                'test_request' => Setting::get('test_request', '0'),
                'contact_message' => Setting::get('contact_message', '0'),
            ];

            $settings = [
                'notifications' => $notificationSettings,
            ];

            return $this->sendResponse($settings, 'Settings retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve settings.', ['error' => $e->getMessage()]);
        }
    }

    public function updateNotification(Request $request)
    {
        try {
            // dd($request->all());
            $validated = Validator::make($request->all(), [
                'user_registration' => 'sometimes|boolean',
                'test_request' => 'sometimes|boolean',
                'contact_message' => 'sometimes|boolean',
            ]);

            if ($validated->fails()) {
                return $this->sendError($validated->errors());
            }

            // only set the values that were present in the payload
            if ($request->has('user_registration')) {
                Setting::set('user_registration', $request->input('user_registration'));
            }
            if ($request->has('test_request')) {
                Setting::set('test_request', $request->input('test_request'));
            }
            if ($request->has('contact_message')) {
                Setting::set('contact_message', $request->input('contact_message'));
            }

            return $this->sendResponse(['message' => 'Notification settings updated successfully.']);
        } catch (\Exception $e) {
            return $this->sendError('Failed to update notification settings.', ['error' => $e->getMessage()]);
        }
    }
}
