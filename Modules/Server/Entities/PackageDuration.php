<?php

namespace Modules\Server\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackageDuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'price'
    ];

    // protected static function newFactory()
    // {
    //     return \Modules\Server\Database\factories\PackageDurationFactory::new();
    // }
}
