<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendWithdrawalNotification extends Notification
{
    use Queueable;

    protected $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
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
        return (new MailMessage)
            ->subject("Withdrawal request Notification")
            ->greeting("Dear " . $this->message['result']->user->firstName . " " . $this->message['result']->user->lastName)
            ->line($this->message['msg'])
            ->line('Withdrawal Amount: ' . number_format($this->message['result']->amount, 2) . '')
            ->line('Withdrawal date: ' . $this->message['result']->created_at . '.')
            ->line('Warmest Regards')
            ->line('Maxcoop Team');
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
