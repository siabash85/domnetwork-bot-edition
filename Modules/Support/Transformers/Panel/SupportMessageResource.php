<?php

namespace Modules\Support\Transformers\Panel;

use Illuminate\Http\Resources\Json\JsonResource;

class SupportMessageResource extends JsonResource
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
            'user' => $this->user,
            'message' => $this->message,
            'answer' => $this->answer,
            'status' => $this->status,
            'created_at' =>  formatGregorian($this->created_at),
        ];
    }
}
