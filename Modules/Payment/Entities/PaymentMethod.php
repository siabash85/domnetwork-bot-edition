<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "description",
        "type",
        "status",
        "is_default",
    ];


    // protected static function newFactory()
    // {
    //     return \Modules\Payment\Database\factories\PaymentMethodFactory::new();
    // }
}
