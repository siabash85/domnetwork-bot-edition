<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Entities\User;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "payment_method_id",
        "paymentable_type",
        "paymentable_id",
        "invoice_id",
        "ref_num",
        "reference_code",
        "amount",
        "status",

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    public function paymentable()
    {
        return $this->morphTo();
    }

    public static function booted()
    {
        static::saving(function ($payment) {
            $payment->reference_code =  random_int(1000000, 10000000);
        });
    }


    // protected static function newFactory()
    // {
    //     return \Modules\Payment\Database\factories\PaymentFactory::new();
    // }
}
