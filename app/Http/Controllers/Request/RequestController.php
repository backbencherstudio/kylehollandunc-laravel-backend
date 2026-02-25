<?php

namespace App\Http\Controllers\Request;

use App\Http\Controllers\Controller;
use App\Models\Request\Request as ModelsRequest;
use Illuminate\Http\Request;
use App\Models\Request\RequestReply;
use App\Notifications\RequestReplyNotification;
use Illuminate\Support\Facades\Notification;
use App\Traits\CommonTrait;

class RequestController extends Controller
{
    use CommonTrait;

    public function index()
    {
        try {
            $contacts = ModelsRequest::latest()->get();
            return $this->sendResponse($contacts, 'Requests retrieved successfully.');
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

            $contact = new ModelsRequest();
            $contact->name = $request->name;
            $contact->email = $request->email;
            $contact->organization = $request->organization;
            $contact->test = $request->test;
            $contact->message = $request->message;
            $contact->save();

            return $this->sendResponse($contact, 'Contact created successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to create contact.', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $contact = ModelsRequest::find($id);

            if (!$contact) {
                return $this->sendError('Contact not found.');
            }

            return $this->sendResponse($contact, 'Contact retrieved successfully.');
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
            // dd($request);

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

            $reply->user_name = $model_request->name;
            // Send notification email to the contact's email address
            Notification::route('mail', $model_request->email)
                ->notify(new RequestReplyNotification($reply));

            return $this->sendResponse($reply, 'Reply sent successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to send reply.', ['error' => $e->getMessage()]);
        }
    }
}
