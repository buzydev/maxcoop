<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendAccountDeactivatedEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user, $status;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, $status)
    {
        $this->user = $user;
        $this->status = $status;
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
        $account_status = $this->status == 'true' ? 'activated' : 'de-activated';


        return (new MailMessage)
            ->line("Dear $firstName")
            ->line("Your $appName account has been $account_status, kindly reach out to us to have the issues sorted in no time.")
            // ->action('Visit Dashboard', url('/'))
            ->line('Thank you for using our application!');
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
