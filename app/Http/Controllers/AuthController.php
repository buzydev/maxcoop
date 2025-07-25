<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use App\Notifications\SendWelcomeEmailNotification;
use App\Notifications\SendDownlineWelcomeEmailNotification;
use App\Notifications\VerificationCode;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (UserService::isUserSuspended($user)) {
            throw ValidationException::withMessages([
                'username' => ['This user has been de-activated, kindly contact admin'],
            ]);
        }

        $cutoffDate = Carbon::parse('2024-09-29');
        if ($user->created_at > $cutoffDate) {
            if (!$user->email_verified_at) {
                return $this->json_failed('Please verify your email before logging in.');
            }
        }

        $data = ['token' => $user->createToken($request->username)->plainTextToken, 'user' => $user];

        return $this->json_success('Login successful!', $data);
    }

    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'referralUsername' => ['required', 'string', 'max:255', 'exists:users,username'],
            'phone' => ['required', 'string', 'max:13', 'min:11'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required'],
        ]);

        if ($validate->fails()) {
            return $this->json_failed('Validation failed', $validate->errors(), 422);
        }

        $user = User::create([
            'firstName' => request('firstName'),
            'lastName' => request('lastName'),
            'email' => request('email'),
            'phone' => request('phone'),
            'username' => strtolower(request('username')),
            'referralUsername' => request('referralUsername'),
            'password' => Hash::make(request('password')),
        ]);

        $user->accountDetail()->create([]); //should happen inside event sef

        $user->notify(new SendWelcomeEmailNotification($user));

        $sponsor = User::sponsor($user)->first();

        if ($sponsor) {
            $sponsor->notify(new SendDownlineWelcomeEmailNotification($user));
        }

        return $this->json_success('Login Successful', $user);
    }

    public function initiate_verify_email(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);

        $user = User::where(function ($query) use ($request) {
            $query->where('email', $request->email)
                ->orWhere('username', $request->email);
        })->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 400);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the code in cache for 15 minutes
        Cache::put('verification_code_' . $user->id, $code, now()->addMinutes(15));

        // Send email
        $user->notify(new VerificationCode($code));


        return response()->json(['message' => 'Verification code sent successfully']);
    }

    public function verify_email(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where(function ($query) use ($request) {
            $query->where('email', $request->email)
                ->orWhere('username', $request->email);
        })->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 400);
        }

        $cachedCode = Cache::get('verification_code_' . $user->id);

        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json(['message' => 'Invalid or expired verification code'], 400);
        }

        $user->email_verified_at = now();
        $user->save();

        Cache::forget('verification_code_' . $user->id);

        return response()->json(['message' => 'Email verified successfully']);
    }



    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Initiates the password reset process for a user by sending a verification code to their email.
     *
     * Validates the request for the presence of an email. Searches for the user with the provided
     * email or username. If the user is found, generates a 6-digit verification code, stores it
     * in the cache for 15 minutes, and sends it to the user's email via a notification.
     * Returns a success message if the process is initiated successfully, otherwise returns
     * an error message if the user is not found.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    /*******  22514395-37a5-4529-86d6-91853bbf4439  *******/
    public function initiate_password_reset(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);

        $user = User::where(function ($query) use ($request) {
            $query->where('email', $request->email)
                ->orWhere('username', $request->email);
        })->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 400);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the code in cache for 15 minutes
        Cache::put('verification_code_' . $user->id, $code, now()->addMinutes(15));

        // Send email
        $user->notify(new PasswordResetNotification($code));


        return response()->json(['message' => 'Verification code sent successfully']);
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Verify password reset email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /*******  7be10837-98e8-4ce0-85a1-fc5944359748  *******/
    public function verify_password_reset_email(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where(function ($query) use ($request) {
            $query->where('email', $request->email)
                ->orWhere('username', $request->email);
        })->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 400);
        }

        $cachedCode = Cache::get('verification_code_' . $user->id);

        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json(['message' => 'Invalid or expired verification code'], 400);
        }

        $user->email_verified_at = now();
        $user->save();

        Cache::forget('verification_code_' . $user->id);

        return response()->json(['message' => 'Code verified successfully']);
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Change the password for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /*******  b4412213-8d89-4429-b94e-16fe4defff98  *******/
    public function change_password(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where(function ($query) use ($request) {
            $query->where('email', $request->email)
                ->orWhere('username', $request->email);
        })->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 400);
        }

        $cachedCode = Cache::get('verification_code_' . $user->id);

        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json(['message' => 'Invalid or expired verification code'], 400);
        }
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }


    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return $this->json_success(
            'User logged out successfully'
        );
    }
}
