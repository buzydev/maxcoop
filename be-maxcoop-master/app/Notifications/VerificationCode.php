<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationCode extends Notification
{
    public $code;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Email Verification Code")
            ->line("Dear user")
            ->line("Your verification code is: $this->code")
            ->line('This code will expire in 15 minutes.')
            ->line('Best regards');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
