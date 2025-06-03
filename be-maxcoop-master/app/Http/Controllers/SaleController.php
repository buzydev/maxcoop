<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function getSales()
    {
        try {
            $result = auth()->user()->sales()->get();
            return $this->json_success('Sales fetched successfully', $result, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }

    public function postSales(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'property_id' => ['required'],
                'imageUrl' => ['required', 'url'],
                'unit' => ['required'],
                'price' => ['required'],
            ]);

            if ($validate->fails()) {
                return $this->json_failed('Validation failed', $validate->errors(), 422);
            }

            $result = auth()->user()->sales()->create([
                'unit' => $request->unit,
                'property_id' => $request->property_id,
                'imageUrl' => $request->imageUrl,
                'price' => $request->price,
            ]);
            return $this->json_success('Sales fetched successfully', $result, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }
}
