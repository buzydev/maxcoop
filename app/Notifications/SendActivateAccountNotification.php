<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendActivateAccountNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user, $planName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, string $planName)
    {
        $this->user = $user;
        $this->planName = $planName;
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
        $firstName = ucfirst($this->user->firstName);
        $appName = ucfirst(env('APP_NAME'));
        $planName = ucfirst($this->planName);

        return (new MailMessage)
            ->subject('Your account has been activated')
            ->line("Dear $firstName,")
            ->line("We're thrilled to share the great news: your account with $appName is now activated and upgraded to the '$planName' plan!")
            ->line('Get ready for an upgraded experience that takes things to the next level.');
        // ->action('Notification Action', url('/'));
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
