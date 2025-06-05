<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * SCOPES
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'SUCCESS');
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Scope a query to only include pending withdrawals.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */

    /*******  a0880376-7b56-4c48-9e7a-b9d3f4e71581  *******/
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }
}
