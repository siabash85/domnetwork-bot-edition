<?php

namespace Modules\User\Entities;


// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Modules\Order\Entities\PreOrder;
use Laravel\Passport\HasApiTokens;
use Modules\Server\Entities\Subscription;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'uid',
        'email',
        'password',
        'section',
        'step',
        'mobile',
        'wallet',
        'status'
    ];

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
        'password' => 'hashed',
    ];

    public function pre_order()
    {
        return $this->hasOne(PreOrder::class);
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
