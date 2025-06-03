<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DownlineController extends Controller
{
    public function myDownlines()
    {
        $user = auth()->user();

        $downlines = User::downlines($user)->get()->toArray();

        return $this->json_success('Downlines fetched', $downlines);
    }
}
