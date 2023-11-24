<?php

namespace Modules\Server\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Server\Entities\PackageDuration;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExtensionService extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "subscription_id",
        "package_duration_id",
        "volume",
        "status",
        "total"
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
    public function package_duration()
    {
        return $this->belongsTo(PackageDuration::class);
    }
}
