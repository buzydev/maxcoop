<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Events\AccountActivateEvent;
use App\Helpers;
use App\Models\Account;
use Exception;

class PaymentController extends Controller
{
    public function initializePayment(Request $request)
    {
        try {
            $user = auth()->user();

            $is_validated = Account::where('user_id', $user->id)
                ->where('status', config('constants.accountStatus.1'))
                ->exists();

            if ($is_validated) {
                throw new Exception('User already validated');
            }

            $request->validate(['plan_id' => 'required']);

            $plan = Helpers::findPlan($request->plan_id);

            $data = [
                'email' => $user->email,
                'amount' => $plan['amount'] * 100,
                'plan_id' => $plan['id'],
                'pubKey' => getenv('PAYSTACK_PUBLIC_KEY')
            ];

            return $this->json_success('Payment details fetched successfully', $data, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function verifyPayment(Request $request)
    {
        try {
            $user = auth()->user();

            $request->validate([
                'txref' => 'required',
                'amount' => 'required',
                'plan_id' => 'required'
            ]);

            $plan_id = $request->plan_id;

            // Make API call to Paystack to verify the transaction
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . getenv('PAYSTACK_SECRET_KEY'),
                'Accept' => 'application/json',
            ])->get("https://api.paystack.co/transaction/verify/{$request->txref}");

            if ($response->failed()) {
                throw new Exception('Failed to verify transaction with Paystack');
            }

            $paystackData = $response->json();

            // Check if the transaction was successful
            if ($paystackData['data']['status'] !== 'success') {
                throw new Exception('Transaction was not successful');
            }

            // Verify the amount
            $amount = $paystackData['data']['amount']; // Paystack amount is in kobo

            if ($amount !== $request->amount) {
                throw new Exception('Amount mismatch');
            }

            $user->account()->create([
                'imageUrl' => "https://res.cloudinary.com//dbdpkkfaf//image//upload//v1726996965//uploads//m9dmdkt0mfasth0jxnvo.png",
                'paymentDate' => now(),
                'status' => config('constants.accountStatus.1')
            ]);

            $user->transactions()->create([
                'reference' => $request->txref,
                'amount' => $amount,
                'type' => 'credit',
            ]);

            event(new AccountActivateEvent($user, $plan_id));

            return $this->json_success('Payment successful', null, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }
}
