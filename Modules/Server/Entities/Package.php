<?php

namespace Modules\Server\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active'
    ];

    // protected static function newFactory()
    // {
    //     return \Modules\Server\Database\factories\PackageFactory::new();
    // }
}
