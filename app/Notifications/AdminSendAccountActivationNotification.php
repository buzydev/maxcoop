<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminSendAccountActivationNotification extends Notification
{
    use Queueable;

    public $activation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($activation)
    {
        $this->activation = $activation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $appName = ucfirst(env('APP_NAME'));

        return (new MailMessage)
            ->subject('Account activation request from user')
            ->line("Dear Admin")
            ->line("User with request is received and will be upgraded to the chosen plan!")
            ->line('Kindly check list and approve or reject the account activation request.')
            ->line('User Detail:')
            ->line('Full name' . $this->activation->user->firstName . ' ' . $this->activation->user->lastName)
            ->line('Email' . $this->activation->user->email)
            ->line('Date of request' . $this->activation->created_at->format('Y-m-d'))
            ->line('Best regards');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
