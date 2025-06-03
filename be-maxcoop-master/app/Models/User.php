<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * RELATIONSHIPS
     */
    // @todo: name should be activate requests
    public function account()
    {
        return $this->hasMany('App\Models\Account');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }

    public function accountDetail()
    {
        return $this->hasOne('App\Models\AccountDetail');
    }

    public function withdrawals()
    {
        return $this->hasMany('App\Models\Withdrawal');
    }

    public function contributions()
    {
        return $this->hasMany('App\Models\Contribution');
    }

    public function sales()
    {
        return $this->hasMany('App\Models\Sale');
    }

    public function earnings()
    {
        return $this->hasMany('App\Models\Earning');
    }

    public function properties()
    {
        return $this->hasMany('App\Models\Property');
    }

    /**
     * SCOPES
     */
    public function scopeDownlines($query, User $user)
    {
        return $query->where('referralUsername', $user->username);
    }

    public function scopeSponsor($query, User $user)
    {
        return $query->where('username', $user->referralUsername);
    }

    public function scopeMember($query)
    {
        return $query->where('role', config('constants.roles.2'));
    }
}
