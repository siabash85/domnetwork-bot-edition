<?php

namespace Modules\Server\Transformers\Panel;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'server' => $this->server,
            'package_duration' => $this->package_duration,
            'package' => $this->package,
            'price' => round($this->price),
            'status' => $this->status,
            'link' => $this->link,
            'created_at' => $this->created_at,
        ];
    }
}
