<?php

namespace App\Http\Controllers;

use App\Services\DownlineService;
use App\Services\EarningService;
use App\Services\WithdrawalService;
use Illuminate\Http\Request;

class StatController extends Controller
{
    public function dashboard()
    {
        try {
            $user = auth()->user();

            $data = [
                'earnedAmount' => EarningService::myTotalEarning($user),
                'salesBonus' => EarningService::salesBonus($user),
                'downlines' => DownlineService::myDownlineCount($user),
                'indirectBonus' => EarningService::myIndirectBonuses($user)
            ];

            return $this->json_success('Dashboard Stat fetched successfully', $data, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function withdrawals()
    {
        try {
            $user = auth()->user();

            $data = [
                'availableBalance' => EarningService::availableBalance($user),
                'successfulWithdrawals' => WithdrawalService::successfulWithdrawals($user),
                'pendingWithdrawals' => WithdrawalService::pendingWithdrawals($user),
            ];

            return $this->json_success('Withdrawal Stat fetched successfully', $data, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }
}
