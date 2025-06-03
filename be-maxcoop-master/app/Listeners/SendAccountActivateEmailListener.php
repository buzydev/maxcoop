<?php

namespace App\Listeners;

use App\Notifications\SendActivateAccountNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAccountActivateEmailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $user = $event->user;
        $planName = $event->planName;
        $user->notify(new SendActivateAccountNotification($user, $planName));
    }
}
