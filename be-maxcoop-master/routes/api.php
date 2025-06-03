<?php

use App\Http\Controllers\ActivateAccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\DownlineController;
use App\Http\Controllers\EarningController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::post('auth/login', [AuthController::class, 'login'])->name('login');
    Route::post('auth/register', [AuthController::class, 'register'])->name('register');
    Route::post('auth/initiate-password-reset', [AuthController::class, 'initiate_password_reset'])->name('initiate_password_reset');
    Route::post('auth/initiate-verify-email', [AuthController::class, 'initiate_verify_email'])->name('initiate_verify_email');
    Route::post('auth/verify-email', [AuthController::class, 'verify_email'])->name('verify_email');
});

Route::get('test/email', function () {
    Mail::raw('Hello World!', function ($msg) {
        $msg->to('adelodundamilare@yahoo.com')->subject('Test Email');
    });
    return 'Mail Sent';
})->name('testEmail');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('logout');
    // Route::post('auth/register', [AuthController::class, 'register'])->name('register');
    Route::post('me/upload-image', [ProfileController::class, 'uploadImage'])->name('patch.uploadImage');

    // PROFILE
    Route::get('me', [ProfileController::class, 'me'])->name('me');
    Route::patch('me/update-profile', [ProfileController::class, 'updateProfile'])->name('patch.updateProfile');
    Route::patch('me/update-password', [ProfileController::class, 'updatePassword'])->name('patch.updatePassword');
    Route::patch('me/add-bank-details', [ProfileController::class, 'addBankDetails'])->name('post.addBankDetails');
    Route::patch('me/update-account-details', [ProfileController::class, 'updateAccountDetail'])->name('patch.updateAccountDetail');
    Route::get('me/bank-details', [ProfileController::class, 'getBankDetail'])->name('post.getBankDetail');

    //
    Route::post('/payment/initialize', [PaymentController::class, 'initializePayment']);
    Route::post('/payment/verify', [PaymentController::class, 'verifyPayment']);

    // MEMBER
    // WITHDRAWALS
    Route::get('member/withdrawals', [WithdrawalController::class, 'getMyWithdrawals'])->name('getMyWithdrawals');
    Route::post('member/withdrawal-request', [WithdrawalController::class, 'postRequestWithdrawal'])->name('postRequestWithdrawal');

    // DOWNLINES
    Route::get('me/downlines', [DownlineController::class, 'myDownlines'])->name('get.my-downlines');

    // ACTIVATE ACCOUNT
    Route::post('account/request-activate', [ActivateAccountController::class, 'create'])->name('post.account.activate-request');
    Route::get('member/activate/requests', [ActivateAccountController::class, 'memberRequests'])->name('get.member.account.requests');

    // CONTRIBUTIONS
    Route::get('member/contributions', [ContributionController::class, 'getContributions'])->name('getContributions');

    // PROPERTIES
    Route::get('member/properties', [PropertyController::class, 'getProperties'])->name('getProperties');

    // SALES
    Route::get('member/sales', [SaleController::class, 'getSales'])->name('getSales');
    Route::post('member/sales', [SaleController::class, 'postSales'])->name('postSales');

    // EARNINGS
    Route::get('member/earnings', [EarningController::class, 'getEarnings'])->name('getEarnings');

    // STATS
    Route::get('stats/dashboard', [StatController::class, 'dashboard'])->name('getStatDashboard');
    Route::get('stats/withdrawals', [StatController::class, 'withdrawals'])->name('getStatWithdrawal');
});

// ADMIN
Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    // USER
    Route::get('admin/users', [UserController::class, 'read'])->name('get.admin.users');
    Route::get('admin/users/{user}', [UserController::class, 'readSingle'])->name('get.admin.users.single');
    Route::post('admin/users/updateStatus', [UserController::class, 'updateStatus'])->name('get.admin.users.updateStatus');
    Route::get('admin/users/{user}/downlines', [UserController::class, 'downlines'])->name('get.admin.users.downlines');

    // HACK: FIX BONUS
    // Route::post('hack/fix-bonus', [EarningController::class, 'fixEarnings'])->name('hack.fix-bonus');

    // EARNINGS
    Route::get('admin/earnings', [EarningController::class, 'getAdminEarnings'])->name('getAdminEarnings');

    // ACCOUNT
    Route::get('account/requests', [ActivateAccountController::class, 'read'])->name('get.account.requests');
    Route::post('account/activate', [ActivateAccountController::class, 'activate'])->name('patch.account.activate');
    Route::patch('account/reject/{account}', [ActivateAccountController::class, 'reject'])->name('patch.account.reject');
});


// Route::fallback(function () {
//     return response()->json_failed([
//         'message' => 'Page not found',
//         'status' => 404,
//     ], 404);
// });
