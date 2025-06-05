<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WithdrawalController extends Controller
{
    public function getMyWithdrawals()
    {
        try {
            $result = auth()->user()->withdrawals()->get();
            return $this->json_success('Withdrawals Fetched Successfully', $result, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function postRequestWithdrawal(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'amount' => ['required'],
            ]);

            if ($validate->fails()) {
                return $this->json_failed('Validation failed', $validate->errors(), 422);
            }

            $result = auth()->user()->withdrawals()->create([
                'status' => 'PENDING',
                'amount' => $request->amount
            ]);
            return $this->json_success('Withdrawals Request Sent', $result, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }
}
