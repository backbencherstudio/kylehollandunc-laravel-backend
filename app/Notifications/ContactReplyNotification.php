<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactReplyNotification extends Notification
{

    private $reply;

    /**
     * Create a new notification instance.
     */
    public function __construct($reply)
    {
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Lake Norman Labs - Reply to Your Contact Message')
            ->view('emails.contact-reply', [
                'reply' => $this->reply,
                'user' => $notifiable,
            ]);
        // ->line('You have received a reply to your contact message.')
        // ->line('Subject: ' . $this->reply->subject)
        // ->line('Description: ' . $this->reply->description)
        // ->action('View Contact', url('/contacts/' . $this->reply->contact_id))
        // ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
