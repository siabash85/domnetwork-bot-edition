<?php

namespace Modules\Support\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Entities\User;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "message",
        "status",
        "answer"
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
