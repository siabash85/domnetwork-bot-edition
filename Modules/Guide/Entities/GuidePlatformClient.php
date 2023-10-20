<?php

namespace Modules\Guide\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuidePlatformClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'guide_platform_id',
        'name',
        'description',
        'status',
        'link',
        'video'
    ];

    public function guide_platform()
    {
        return $this->belongsTo(GuidePlatform::class);
    }

    // protected static function newFactory()
    // {
    //     return \Modules\Guide\Database\factories\GuidePlatformClientFactory::new();
    // }
}
