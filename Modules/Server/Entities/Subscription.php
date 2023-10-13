<?php

namespace Modules\Server\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Entities\User;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'expire_at',
        'status'
    ];
    protected $casts = [
        'expire_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // protected static function newFactory()
    // {
    //     return \Modules\Server\Database\factories\SubscriptionFactory::new();
    // }
}
