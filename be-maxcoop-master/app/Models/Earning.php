<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function propertyUser()
    {
        return $this->hasOneThrough(Property::class, User::class);
    }

    public function scopeSale($query)
    {
        return $query->where('type', config('constants.earningType.1'));
    }

    public function scopeActivation($query)
    {
        return $query->where('type', config('constants.earningType.0'));
    }
}
