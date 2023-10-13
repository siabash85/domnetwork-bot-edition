<?php

namespace Modules\Server\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'stock',
        'is_active',
        'is_default',

    ];

    // protected static function newFactory()
    // {
    //     return \Modules\Server\Database\factories\ServerFactory::new();
    // }
}
