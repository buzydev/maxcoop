<?php

namespace App\Services;

use App\Models\User;

class DownlineService
{
    public static function myDownlineCount(User $user)
    {
        return User::downlines($user)->count();
    }
}
