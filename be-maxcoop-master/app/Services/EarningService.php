<?php

namespace App\Services;

use App\Models\User;

class EarningService
{
    public static function myTotalEarning(User $user)
    {
        return $user->earnings()->sum('amount');
    }

    public static function myIndirectBonuses(User $user)
    {
        return $user->earnings()->where('description', 'like', 'second %')->sum('amount');
    }

    public static function salesBonus(User $user)
    {
        return $user->earnings()->sale()->sum('amount');
    }

    public static function availableBalance(User $user)
    {
        $totalEarnings = self::myTotalEarning($user);
        $successfulWithdrawals = WithdrawalService::activeWithdrawals($user);
        return $totalEarnings - $successfulWithdrawals;
    }
}
