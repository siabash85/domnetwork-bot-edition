<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ev_number',
        'ev_code',
        'amount',
        'status',
    ];
}
