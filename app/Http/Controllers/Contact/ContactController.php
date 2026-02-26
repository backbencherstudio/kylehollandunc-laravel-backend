<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact\Contact;
use App\Models\Contact\ContactReply;
use App\Models\Setting\Setting;
use App\Notifications\AdminNotify;
use App\Notifications\ContactReplyNotification;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    use CommonTrait;

    public function index()
    {
        try {
            $contacts = Contact::latest()->paginate(10);
            return $this->sendResponse($contacts, 'Contacts retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve contacts.', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            // dd($request->all());
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'order_id' => 'nullable|max:255',
                'message' => 'required|string',
            ]);

            $contact = new Contact();
            $contact->name = $request->name;
            $contact->email = $request->email;
            $contact->order_id = $request->order_id;
            $contact->message = $request->message;
            $contact->save();

            if (Setting::get('contact_message') == "true") {
                $adminEmail = env('ADMIN_EMAIL');
                if ($adminEmail) {
                    $subject = 'New Contact Message';
                    $data = [
                        'name' => $request->name,
                        'email' => $request->email,
                        'Message' => $request->message
                    ];
                    Notification::route('mail', $adminEmail)->notify(new AdminNotify($subject, $data));
                }
            }

            return $this->sendResponse($contact, 'Contact created successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to create contact.', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $contact = Contact::find($id);

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
        $contact = Contact::find($id);

        if (!$contact) {
            return $this->sendError('Contact not found.');
        }

        $contact->delete();

        return $this->sendResponse([], 'Contact deleted successfully.');
    }

    public function reply(Request $request, $id)
    {
        try {
            $contact = Contact::find($id);

            if (!$contact) {
                return $this->sendError('Contact not found.');
            }

            $request->validate([
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $reply = new ContactReply();
            $reply->contact_id = $contact->id;
            $reply->subject = $request->subject;
            $reply->description = $request->description;
            $reply->save();

            $reply->user_name = $contact->name;
            // Send notification email to the contact's email address
            Notification::route('mail', $contact->email)
                ->notify(new ContactReplyNotification($reply));

            return $this->sendResponse($reply, 'Reply sent successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to send reply.', ['error' => $e->getMessage()]);
        }
    }
}
