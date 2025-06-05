<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContributionController extends Controller
{
    public function getContributions()
    {
        try {
            $result = auth()->user()->contributions()->get();
            return $this->json_success('Contributions fetched successfully', $result, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }
}
