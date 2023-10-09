<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Server\Entities\Package;
use Modules\Server\Entities\PackageDuration;
use Modules\Server\Entities\Server;
use Modules\User\Entities\User;

class PreOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'server_id',
        'package_id',
        'package_duration_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function package_duration()
    {
        return $this->belongsTo(PackageDuration::class);
    }
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    // protected static function newFactory()
    // {
    //     return \Modules\Order\Database\factories\PreOrderFactory::new();
    // }
}
