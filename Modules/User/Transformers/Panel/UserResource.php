<?php

namespace Modules\User\Transformers\Panel;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'first_name' => $this->first_name,
            'uid' => $this->uid,
            'email' => $this->email,
            'is_superuser' => $this->is_superuser,
            'is_notifable' => $this->is_notifable,
            'is_partner' => $this->is_partner,
            'wallet' => $this->wallet,
            'status' => $this->status,
            'created_at' =>  formatGregorian($this->created_at),
        ];
    }
}
