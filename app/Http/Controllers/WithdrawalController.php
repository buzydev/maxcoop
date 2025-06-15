<?php

namespace App\Http\Controllers;

use App\Models\CoopPayment;
use App\Models\Withdrawal;
use App\Notifications\SendAdminWithdrawalNotification;
use App\Notifications\SendWithdrawalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
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
            $message = [
                'result' => $result,
                'msg' => 'Your withdrawal requests has been received, currently awaiting approval.'
            ];
            //For User
            Notification::send(auth()->user(), new SendWithdrawalNotification($message));
            //Admin
            Notification::route('mail', [config('constants.admin_email') => "Withdrawal Request"])->notify(new SendAdminWithdrawalNotification($message));
            return $this->json_success('Withdrawals Request Sent', $result, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }


    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Retrieve all withdrawal records in descending order.
     *
     * This method fetches the latest withdrawals from the database
     * and returns them in a JSON response. In case of an exception, an error
     * message is returned.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing the
     *         withdrawals or an error message.
     */

    /*******  b591c7ab-34df-4bad-8a9f-ca1ada52b4a6  *******/
    public function GetAllWithdraws()
    {
        try {
            $result = Withdrawal::with('user')->latest()->get();
            return $this->json_success('Withdrawals Fetched Successfully', $result, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Update a withdrawal request.
     *
     * @param Request $request
     * @param int $withdraw_id
     * @return \Illuminate\Http\JsonResponse
     */
    /*******  b1162236-6ca0-4879-89ce-1812354fe941  *******/
    public function UpdateWithdraw(Request $request, $withdraw_id)
    {
        $validate = Validator::make($request->all(), [
            'status' => ['required', 'in:PENDING,SUCCESS,REJECTED'],
        ]);

        if ($validate->fails()) {
            return $this->json_failed('Validation failed', $validate->errors(), 422);
        }
        $withdraw = Withdrawal::whereId($withdraw_id)->first();
        if ($withdraw) {
            if ($withdraw->status == "success") {
                return $this->json_failed('withdraw request already approved');
            }
            $withdraw->status = $request->status;
            $withdraw->save();
            //Send Notification to user
            $message = [
                'result' => $withdraw,
                'msg' => 'Your withdrawal requests has been ' . $request->status
            ];
            Notification::send($withdraw->user, new SendWithdrawalNotification($message));
            return $this->json_success('Withdrawal ' . $request->status, $withdraw);
        } else {
            return $this->json_failed("withdraw request not found");
        }
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Retrieve all cooperative payment records in descending order.
     *
     * This method fetches the latest cooperative payments from the database
     * and returns them in a JSON response. In case of an exception, an error
     * message is returned.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing the
     *         cooperative payments or an error message.
     */

    /*******  02a174e0-c350-4e79-8f07-84a8c0045ac0  *******/
    public function GetAllCoopPayments()
    {
        try {
            $result = CoopPayment::with('user')->latest()->get();
            return $this->json_success('Coop Payments Fetched Successfully', $result, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }


    public function UpdateCoopPayment(Request $request, $payment_id)
    {
        $validate = Validator::make($request->all(), [
            'status' => ['required', 'in:PENDING,SUCCESS,REJECTED'],
        ]);

        if ($validate->fails()) {
            return $this->json_failed('Validation failed', $validate->errors(), 422);
        }
        $payment = CoopPayment::whereId($payment_id)->first();
        if ($payment) {
            if ($payment->status == "success") {
                return $this->json_failed('payment request already approved');
            }
            $payment->status = $request->status;
            $payment->save();
            //Send Notification to user
            // $message = [
            //     'result' => $payment,
            //     'msg' => 'Your withdrawal requests has been ' . $request->status
            // ];
            // Notification::send($withdraw->user(), new SendWithdrawalNotification($message));
            return $this->json_success('Payment Updated ' . $request->status, $payment);
        } else {
            return $this->json_failed("Payment request not found");
        }
    }
}
