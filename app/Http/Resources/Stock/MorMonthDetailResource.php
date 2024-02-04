<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\ItemProduct\ItemProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MorMonthDetailResource extends JsonResource
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
            'item_product' => new ItemProductResource($this->item_product),
            'price' => $this->price,
            'last_stock' => $this->last_stock,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
