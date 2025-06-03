<?php

namespace App;

class Helpers
{

    static function findPlan(String $planName)
    {
        $plans = config('constants.plans');

        $result = null;

        foreach ($plans as $plan) {
            if ($plan['id'] == $planName) {
                $result = $plan;
            }
        }

        return $result;
    }
}
