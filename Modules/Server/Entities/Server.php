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
        'username',
        'password',
        'address',
        'inbound',
        "type"
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    // protected static function newFactory()
    // {
    //     return \Modules\Server\Database\factories\ServerFactory::new();
    // }
}
