<?php

namespace Modules\Server\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pricing extends Model
{
    use HasFactory;


    protected $fillable = [
        'content',
        'name',
        'is_default',
    ];
}
