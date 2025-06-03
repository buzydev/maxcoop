<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendBonusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user, $amount, $name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user,  $amount, $name)
    {
        $this->user = $user;
        $this->name = $name;
        $this->amount = $amount;
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
        $amount = ucfirst($this->amount);
        $name = ucfirst($this->name);

        return (new MailMessage)
            ->subject('You earned a Bonus')
            ->line("Hello $firstName")
            ->line("We're thrilled to inform you have just earned a new $name. Get ready to unlock a world of possibilities and enjoy the added convenience and benefits.")
            ->line("The credited amount is NGN$amount. You now have an increased balance in your wallet, allowing you to make seamless transactions and take advantage of our services with ease.")
            ->line("Thank you for being a valued customer. We appreciate your trust and look forward to serving you with utmost dedication.");
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
