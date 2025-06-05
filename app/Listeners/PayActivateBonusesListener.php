<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Helpers;
use App\Notifications\SendBonusNotification;

class PayActivateBonusesListener
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
        $downline = $event->user;
        $planName = $event->planName;
        $sponsor = User::sponsor($downline)->first();

        if (!$sponsor) {
            throw new \Exception('Sponsor not found');
        }

        $plan =  Helpers::findPlan($planName);

        if (!$plan) {
            throw new \Exception('Invalid plan selected, please try again');
        }

        $this->firstLevelBonus($sponsor, $plan['amount'], $downline);

        $sponsorSponsor = User::sponsor($sponsor)->first();
        if (!$sponsorSponsor) return;

        $this->secondLevelBonus($sponsorSponsor, $plan['amount'], $downline);
    }

    protected function firstLevelBonus(User $user, float $planAmount, User $downline)
    {
        $amount = 15 / 100 * $planAmount;
        // 8% of
        $user->earnings()->create([
            'description' => 'First Level account activation bonus',
            'amount' => $amount,
            'type' => config('constants.earningType.0.id'), //account activation
            'metadata' => json_encode($downline),
        ]);

        // send bonus
        $user->notify(new SendBonusNotification($user, $amount, 'First Level Bonus'));
    }

    protected function secondLevelBonus(User $user, float $planAmount, User $downline)
    {
        $amount = 5 / 100 * $planAmount;
        // 5% of
        $user->earnings()->create([
            'description' => 'Second Level Account activation bonus',
            'amount' => $amount,
            'type' => config('constants.earningType.0.id'), //account activation
            'metadata' => json_encode($downline),
        ]);
        // send bonus
        $user->notify(new SendBonusNotification($user, $amount, "Second Level Activation Bonus"));
    }
}
