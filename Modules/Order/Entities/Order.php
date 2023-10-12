<?php

namespace Modules\Order\Entities;

use Modules\User\Entities\User;
use Modules\Payment\Entities\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Server\Entities\Package;
use Modules\Server\Entities\PackageDuration;
use Modules\Server\Entities\Server;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "server_id",
        "package_duration_id",
        "package_id",
        "reference_code",
        "status",
        "payable_price",
        "price",

    ];

    public static function booted()
    {
        static::saving(function ($payment) {
            $payment->reference_code =  random_int(1000000, 10000000);
        });
    }
    public function payments()
    {
        return $this->morphMany(Payment::class, "paymentable");
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function server()
    {
        return $this->belongsTo(Server::class);
    }
    public function package_duration()
    {
        return $this->belongsTo(PackageDuration::class);
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    // protected static function newFactory()
    // {
    //     return \Modules\Order\Database\factories\OrderFactory::new();
    // }
}
