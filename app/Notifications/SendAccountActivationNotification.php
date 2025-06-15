<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendAccountActivationNotification extends Notification
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
        // $this->planName = $planName;
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
            ->subject('Your account activation request is received')
            ->line("Dear " . $this->activation->user->firstName)
            ->line("We're thrilled to share the great news: your activation account request is received and will be upgraded to the chosen plan!")
            ->line('Get ready for an upgraded experience that takes things to the next level.');
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
