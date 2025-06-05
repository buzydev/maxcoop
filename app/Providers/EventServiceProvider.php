<?php

namespace App\Providers;

use App\Events\AccountActivateEvent;
use App\Events\AccountRejectEvent;
use App\Events\PasswordUpdateEvent;
use App\Listeners\ListenerSendPasswordUpdateEmail;
use App\Listeners\PayActivateBonusesListener;
use App\Listeners\SendAccountActivateEmailListener;
use App\Listeners\SendAccountRejectEmailListener;
use App\Listeners\SendWelcomeEmailListener;
use App\Listeners\UpdateUserPlanListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            // SendEmailVerificationNotification::class,
            SendWelcomeEmailListener::class
        ],
        PasswordUpdateEvent::class => [
            ListenerSendPasswordUpdateEmail::class
        ],
        AccountActivateEvent::class => [
            UpdateUserPlanListener::class,
            PayActivateBonusesListener::class,
            SendAccountActivateEmailListener::class
        ],
        AccountRejectEvent::class => [
            SendAccountRejectEmailListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
