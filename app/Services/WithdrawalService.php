<?php

namespace App\Services;

use App\Models\User;

class WithdrawalService
{
    public static function availableBalance(User $user): float
    {
        return $user->earnings()->sum('amount');
    }

    public static function successfulWithdrawals(User $user): float
    {
        return $user->withdrawals()->successful()->count();
    }

    public static function successfulWithdrawalSum(User $user): float
    {
        return $user->withdrawals()->successful()->sum('amount');
    }

    public static function pendingWithdrawals(User $user): float
    {
        return $user->withdrawals()->pending()->count();
    }

    public static function activeWithdrawals(User $user): float
    {
        $pending = $user->withdrawals()->pending()->sum('amount');
        $successful = $user->withdrawals()->successful()->sum('amount');
        return $pending + $successful;
    }
}
