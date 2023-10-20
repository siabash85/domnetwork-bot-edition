<?php

namespace Modules\Guide\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuidePlatform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    public function clients()
    {
        return $this->hasMany(GuidePlatformClient::class, 'id', 'guide_platform_id');
    }

    // protected static function newFactory()
    // {
    //     return \Modules\Guide\Database\factories\GuidePlatformFactory::new();
    // }
}
