<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\Location\LocationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductStockResource extends JsonResource
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
            'item_product' => [
                'id' => $this->item_product->id,
                'code' => $this->item_product->code,
                'name' => $this->item_product->name,
                'brand' => $this->item_product->brand,
                'size' => $this->item_product->size,
                'unit' => $this->item_product->unit,
                'location' => new LocationResource($this->item_product->location),
                'price' => $this->item_product->price,
            ],
            'location' => new LocationResource($this->location),
            'stock' => !empty($this->stock) ? $this->stock : 0,
            'updated_at' => $this->updated_at,
        ];
    }
}
