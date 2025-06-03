<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Models\Earning;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EarningController extends Controller
{

    public function getEarnings()
    {
        try {
            $user = auth()->user();

            $result = $user->earnings()->with(['user' => function ($query) {
                $query->select(['id', 'firstName', 'lastName', 'email']);
            }])->get()->map(function ($earning) {
                $metadata = json_decode($earning->metadata, true);
                $plan_name = $metadata['plan'] ?? null;
                $plan = Helpers::findPlan($plan_name);

                return array_merge($earning->toArray(), [
                    'plan' => $plan_name,
                    'plan_amount' => $plan['amount'] ?? null
                ]);
            });

            return $this->json_success('Earnings fetched successfully', $result, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function getAdminEarnings()
    {
        try {
            $result = Earning::all()->map(function ($earning) {
                $metadata = json_decode($earning->metadata, true);
                $plan_name = $metadata['plan'] ?? null;
                $plan = Helpers::findPlan($plan_name);

                return array_merge($earning->toArray(), [
                    'plan' => $plan_name,
                    'plan_amount' => $plan['amount'] ?? null
                ]);
            });
            return $this->json_success('Earnings fetched successfully', $result, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function fixEarnings()
    {
        try {
            $invalids = collect();
            $processedCount = 0;
            $updatedCount = 0;
            $validAmounts = [25000, 50000, 100000, 500000];

            Earning::query()->chunk(1000, function ($earnings) use (&$invalids, &$processedCount, &$updatedCount, $validAmounts) {
                foreach ($earnings as $earning) {
                    try {
                        $amount = $earning->amount;
                        $isFirstLevelAccount = Str::contains(strtolower($earning->description), 'first level account');

                        $real_amount = $isFirstLevelAccount ? (100 * $amount / 5) : (100 * $amount / 2);
                        $updated_real_amount = $isFirstLevelAccount ? (100 * $amount / 8) : (100 * $amount / 5);
                        $correct_real_amount = $isFirstLevelAccount ? (100 * $amount / 15) : (100 * $amount / 5);

                        if (in_array($real_amount, $validAmounts)) {
                            $new_amount = $isFirstLevelAccount ? ($real_amount * 15 / 100) : ($real_amount * 5 / 100);
                            $earning->amount = $new_amount;
                            $earning->save();
                            $updatedCount++;
                        } else if (in_array($updated_real_amount, $validAmounts)) {
                            $new_amount = $isFirstLevelAccount ? ($real_amount * 15 / 100) : ($real_amount * 5 / 100);
                            $earning->amount = $new_amount;
                            $earning->save();
                            $updatedCount++;
                        } else if (in_array($correct_real_amount, $validAmounts)) {
                            $updatedCount++;
                        } else {
                            $invalids->push([
                                'id' => $earning->id,
                                'amount' => $amount,
                                'description' => $earning->description,
                                'real_amount' => $real_amount
                            ]);
                        }

                        $processedCount++;

                        // Log progress every 10,000 records
                        if ($processedCount % 10000 == 0) {
                            Log::info("Processed $processedCount earnings. Updated: $updatedCount. Invalid: " . $invalids->count());
                        }
                    } catch (\Exception $e) {
                        Log::error("Error processing earning ID {$earning->id}: " . $e->getMessage());
                    }
                }
            });

            // Final log
            Log::info("Processing complete. Total processed: $processedCount. Updated: $updatedCount. Invalid: " . $invalids->count());

            $data = [
                'invalids' => $invalids,
                'processedCount' => $processedCount,
                'updatedCount' => $updatedCount,
                'earnings' => Earning::all()
            ];
            return $this->json_success('Earnings fetched successfully', $data, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }
}
