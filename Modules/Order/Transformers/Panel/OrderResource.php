<?php

namespace Modules\Order\Transformers\Panel;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Server\Transformers\Panel\ServiceResource;

class OrderResource extends JsonResource
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
            'service' => new ServiceResource($this->service),
            'user' => $this->user,
            'price' => round($this->price),
            'payable_price' => $this->payable_price,
            'status' => $this->status,
            'reference_code' => $this->reference_code,
            'created_at' =>  formatGregorian($this->created_at),
        ];
    }
}
