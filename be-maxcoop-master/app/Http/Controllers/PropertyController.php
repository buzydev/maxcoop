<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    //
    public function getProperties()
    {
        try {
            $result = Property::get();
            return $this->json_success('Properties fetched successfully', $result, 200);
        } catch (\Exception $e) {
            return $this->json_failed($e->getMessage());
        }
    }
}
