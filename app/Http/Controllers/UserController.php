<?php

namespace App\Http\Controllers;

use App\Models\AccountDetail;
use App\Models\User;
use App\Notifications\SendAccountDeactivatedEmailNotification;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function read()
    {
        try {
            $requests = User::member()->get();
            return $this->json_success('Users Fetched Successfully', $requests);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function readSingle(User $user)
    {
        try {
            $response = [
                'user' => $user,
                'Yes',
                'profileData' => AccountDetail::where('user_id', $user->id)->first(),
                'referral' => User::sponsor($user)->first()
            ];

            return $this->json_success('Users Fetched Successfully', $response);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function updateStatus(Request $request)
    {
        try {

            $user = User::where('id', $request->user_id)->get();
            $status = $request->status;

            $user->is_active = $status;
            $user->save();

            // send email
            $user->notify(new SendAccountDeactivatedEmailNotification($user, $status));

            // log user out
            UserService::logUserOut($user);

            return $this->json_success('User Account Status Updated Successfully');
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function downlines(User $user)
    {
        try {
            $user = User::downlines($user)->get();
            return $this->json_success('User Downline Fetched Successfully', $user);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }
}
