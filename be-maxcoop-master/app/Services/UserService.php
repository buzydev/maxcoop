<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public static function logUserOut(User $user)
    {
        $user->tokens()->delete();
    }

    public static function isUserSuspended(User $user)
    {
        return $user->is_active == 0;
    }
}
