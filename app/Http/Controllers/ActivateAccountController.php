<?php

namespace App\Http\Controllers;

use App\Events\AccountActivateEvent;
use App\Events\AccountRejectEvent;
use App\Models\Account;
use App\Models\ActivateAccount;
use App\Notifications\AdminSendAccountActivationNotification;
use App\Notifications\SendAccountActivationNotification;
use App\Notifications\SendActivateAccountNotification;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class ActivateAccountController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'imageUrl' => ['required', 'url'],
                'paymentDate' => ['required'],
            ]);

            if ($validate->fails()) {
                return $this->json_failed('Validation failed', $validate->errors(), 422);
            }

            $activation = auth()->user()->account()->create([
                'imageUrl' => $request->imageUrl,
                'paymentDate' => $request->paymentDate
            ]);
            //User
            Notification::send(auth()->user(), new SendAccountActivationNotification($activation));
            //Admin
            Notification::route('mail', [config('constants.admin_email') => "Account Activation Request"])->notify(new AdminSendAccountActivationNotification($activation));
            return $this->json_success('Account activation request sent successfully');
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function read()
    {
        try {
            $requests = Account::with(['user' => function ($query) {
                $query->select(['id', 'firstName', 'lastName', 'email']);
            }])->get();
            return $this->json_success('Account Activation Requests Fetched', $requests);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function memberRequests()
    {
        try {
            $requests = auth()->user()->account()->get();
            return $this->json_success('Account Activation Requests Fetched', $requests);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function activate(Request $request)
    {
        try {

            $validate = Validator::make($request->all(), [
                'accountId' => ['required'],
                'plan' => ['required'],
            ]);

            if ($validate->fails()) {
                return $this->json_failed('Validation failed', $validate->errors(), 422);
            }

            $account = Account::find($request->accountId);

            // @todo: kindly revisit
            // if (\Helpers::findPlan($request->plan)) {
            //     return $this->json_failed('Plan not found, please try again');
            // }

            if (!$account) {
                return $this->json_failed('Account not found');
            }

            // if account status is not INACTIVE
            if ($account->status != config('constants.accountStatus.0')) {
                return $this->json_failed('Account status already updated');
            }

            if ($request->plan == config('constants.plans.0.id')) {
                return $this->json_failed('Invalid plan selected, please try again');
            }

            $user = $account->user;

            // if user is not inactive, user is already activate hence, reject request
            if ($user->plan != config('constants.plans.0.id')) {
                return $this->json_failed('User already activated, kindly refund user and reject request');
            }

            // log user out
            UserService::logUserOut($user);

            $account->status = config('constants.accountStatus.1');
            $account->save();
            event(new AccountActivateEvent($user, $request->plan));
            Notification::send($user, new SendActivateAccountNotification($user, $request->plan));
            return $this->json_success('Account Activation Success', $account);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function reject(Account $account)
    {
        try {
            // if account status is not INACTIVE
            if ($account->status != config('constants.accountStatus.0')) {
                return $this->json_failed('Account status already updated');
            }
            $account->status =  config('constants.accountStatus.2');
            $account->save();
            event(new AccountRejectEvent());
            $user = $account->user;
            Notification::send($user, new SendActivateAccountNotification($user, $account->user->plan));
            return $this->json_success('Account Rejection Success', $account);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Deletes the specified account.
     *
     * @param  Account  $account
     * @return \Illuminate\Http\JsonResponse
     */

    /*******  1412361f-c63a-4bfb-841b-6e7f9ac7c4da  *******/
    function DeleteAccount(Account $account)
    {
        try {
            $account->delete();
            return $this->json_success('Account Deleted Success', []);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }


    //Cooperative Payments

    public function createCoopPayment(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'imageUrl' => ['required', 'url'],
                'paymentType' => ['required'],
                'paymentDate' => ['required'],
            ]);

            if ($validate->fails()) {
                return $this->json_failed('Validation failed', $validate->errors(), 422);
            }

            auth()->user()->coopPayments()->create([
                'imageUrl' => $request->imageUrl,
                'paymentType' => $request->paymentType,
                'paymentDate' => $request->paymentDate
            ]);

            return $this->json_success('Payment request sent successfully');
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function CoopPaymentRequests()
    {
        try {
            $requests = auth()->user()->coopPayments()->get();
            return $this->json_success('Cooperative Payment Requests Fetched', $requests);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }
}
