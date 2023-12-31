<?php

namespace App\Http\Resources\ItemProduct;

use App\Http\Resources\ItemCategory\ItemCategoryResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\Param\ParamResource;
use App\Http\Resources\Supplier\SupplierResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemProductResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'item_category' => new ItemCategoryResource($this->item_category),
            'sub_item_category' => new ItemCategoryResource($this->sub_item_category),
            'brand' => $this->brand,
            'description' => $this->description,
            'size' => $this->size,
            'unit' => new ParamResource($this->unit),
            'tax' => $this->tax,
            'location' => new LocationResource($this->location),
            'supplier' => new SupplierResource($this->supplier),
            'price' => $this->price,
            'sell_price' => $this->sell_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
