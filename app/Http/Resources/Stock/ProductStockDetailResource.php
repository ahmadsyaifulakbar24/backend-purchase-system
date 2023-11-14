<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\ItemProduct\ItemProductResource;
use App\Http\Resources\Location\LocationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductStockDetailResource extends JsonResource
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
            'stock' => !empty($this->stock) ? $this->stock : 0,
            'updated_at' => $this->updated_at,
        ];
    }
}
