<?php

namespace App\Http\Controllers\Request;

use App\Http\Controllers\Controller;
use App\Models\Request\Request as ModelsRequest;
use Illuminate\Http\Request;
use App\Models\Request\RequestReply;
use App\Models\Setting\Setting;
use App\Notifications\AdminNotify;
use App\Notifications\RequestReplyNotification;
use Illuminate\Support\Facades\Notification;
use App\Traits\CommonTrait;

class RequestController extends Controller
{
    use CommonTrait;

    public function index()
    {
        try {
            $getAllRequest = ModelsRequest::latest()->paginate(10);
            return $this->sendResponse($getAllRequest, 'Requests retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve requests.', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            // dd($request->all());
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'organization' => 'nullable|string|max:255',
                'test' => 'nullable|string|max:255',
                'message' => 'required|string',
            ]);

            $makeRequest = new ModelsRequest();
            $makeRequest->name = $request->name;
            $makeRequest->email = $request->email;
            $makeRequest->organization = $request->organization;
            $makeRequest->test = $request->test;
            $makeRequest->message = $request->message;
            $makeRequest->status = 'pending';
            $makeRequest->save();

            if (Setting::get('test_request') == "true") {
                $adminEmail = env('ADMIN_EMAIL');
                if ($adminEmail) {
                    $subject = 'New Test Request';
                    $data = [
                        'name' => $request->name,
                        'email' => $request->email,
                        'organization' => $request->organization,
                        'test' => $request->test
                    ];
                    Notification::route('mail', $adminEmail)->notify(new AdminNotify($subject, $data));
                }
            }

            return $this->sendResponse($makeRequest, 'Request created successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to create contact.', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $makeRequest = ModelsRequest::find($id);

            if (!$makeRequest) {
                return $this->sendError('Contact not found.');
            }

            return $this->sendResponse($makeRequest, 'Contact retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve contact.', ['error' => $e->getMessage()]);
        }
    }

    // public function update(Request $request, $id)
    // {
    //     $contact = Contact::find($id);

    //     if (!$contact) {
    //         return $this->sendError('Contact not found.');
    //     }

    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|max:255',
    //         'organization' => 'nullable|string|max:255',
    //         'test' => 'nullable|string|max:255',
    //         'message' => 'required|string',
    //     ]);

    //     $contact->update($request->all());

    //     return $this->sendResponse($contact, 'Contact updated successfully.');
    // }

    public function destroy($id)
    {
        try {
            $getRequest = ModelsRequest::find($id);

            if (!$getRequest) {
                return $this->sendError('Request not found.');
            }

            $getRequest->delete();

            return $this->sendResponse([], 'Request deleted successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete request.', ['error' => $e->getMessage()]);
        }
    }

    public function reply(Request $request, $id)
    {
        try {
            $model_request = ModelsRequest::find($id);
            // dd($request->email);

            if (!$model_request) {
                return $this->sendError('Request not found.');
            }

            $request->validate([
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $reply = new RequestReply();
            $reply->request_id = $model_request->id;
            $reply->subject = $request->subject;
            $reply->description = $request->description;
            $reply->save();

            // set name on the reply object for the notification
            $reply->user_name = $model_request->name;

            try {
                Notification::route('mail', $request->email ? $request->email : $model_request->email)
                    ->notify(new RequestReplyNotification($reply));

                $model_request->status = 'completed';
                $model_request->save();

                return $this->sendResponse($reply, 'Reply sent and status updated successfully.');
            } catch (\Exception $mailEx) {
                return $this->sendError('Reply saved but failed to send email.', ['error' => $mailEx->getMessage()]);
            }
        } catch (\Exception $e) {
            return $this->sendError('Failed to send reply.', ['error' => $e->getMessage()]);
        }
    }
}
