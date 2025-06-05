<?php

namespace Tests\Feature;

use App\Events\AccountActivateEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PayActivateBonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_paying_first_level_activation_bonus()
    {
        $user = User::factory()->create(['username' => 'tester1']);
        $oldEarningSum = $user->earnings()->sum('amount');
        $plan = config('constants.plans.1'); //['id' => 'PLATINUM', 'amount' => 50,000]
        $downline = User::factory()->create(['referralUsername' => $user->username]);
        AccountActivateEvent::dispatch($downline, $plan['id']);
        // 5% of plan
        $newEarningSum = $user->refresh()->earnings()->sum('amount') - (5 / 100 * $plan['amount']);

        $this->assertTrue($oldEarningSum == $newEarningSum);
    }

    public function test_is_paying_second_level_activation_bonus()
    {
        $user = User::factory()->create(['username' => 'tester2']);
        $oldEarningSum = $user->earnings()->sum('amount');
        $plan = config('constants.plans.1'); //['id' => 'PLATINUM', 'amount' => 50,000]
        $downline = User::factory()->create(['referralUsername' => $user->username]);
        $secondLevelDownline = User::factory()->create(['referralUsername' => $downline->username]);
        AccountActivateEvent::dispatch($secondLevelDownline, $plan['id']);
        // 2% of plan
        $newEarningSum = $user->refresh()->earnings()->sum('amount') - (2 / 100 * $plan['amount']);
        $this->assertTrue($oldEarningSum == $newEarningSum);
    }
}
