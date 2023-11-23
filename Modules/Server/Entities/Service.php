<?php

namespace Modules\Server\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;


    protected $fillable = [
        "server_id",
        "package_duration_id",
        "package_id",
        "status",
        "price",
    ];



    public function server()
    {
        return $this->belongsTo(Server::class);
    }
    public function package_duration()
    {
        return $this->belongsTo(PackageDuration::class);
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // protected static function newFactory()
    // {
    //     return \Modules\Server\Database\factories\ServicesFactory::new();
    // }
}
