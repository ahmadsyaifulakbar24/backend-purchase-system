<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\ItemProduct\ItemProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'location_id' => $this->location_id,
            'item_product' => new ItemProductResource($this->item_product),
            'date' => $this->date,
            'quantity' => $this->quantity,
            'item_price' => $this->item_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
