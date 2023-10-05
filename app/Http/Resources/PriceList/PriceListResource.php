<?php

namespace App\Http\Resources\PriceList;

use App\Http\Resources\ItemProduct\ItemProductResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\Supplier\SupplierResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceListResource extends JsonResource
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
            'location' => new LocationResource($this->location),
            'supplier' => new SupplierResource($this->supplier),
            'item_product' => new ItemProductResource($this->item_product),
            'price' => $this->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
