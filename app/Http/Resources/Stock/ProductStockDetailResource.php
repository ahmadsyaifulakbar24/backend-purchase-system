<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\ItemCategory\ItemCategoryResource;
use App\Http\Resources\ItemProduct\ItemProductResource;
use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\Param\ParamResource;
use App\Http\Resources\Supplier\SupplierResource;
use App\Models\ItemCategory;
use App\Models\Location;
use App\Models\Param;
use App\Models\Supplier;
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
            'item_product' => [
                'id' => $this->item_product_id,
                'code' => $this->code,
                'name' => $this->name,
                'item_category' => new ItemCategoryResource(ItemCategory::find($this->item_category_id)),
                'sub_item_category' => new ItemCategoryResource(ItemCategory::find($this->sub_item_category_id)),
                'brand' => $this->brand,
                'description' => $this->description,
                'size' => $this->size,
                'unit' => new ParamResource(Param::find($this->unit_id)),
                'tax' => $this->tax,
                'location' => new LocationResource(Location::find($this->location_id)),
                'supplier' => new SupplierResource(Supplier::find($this->supplier_id)),
                'price' => $this->price,
            ],
            'stock' => !empty($this->stock) ? $this->stock : 0,
            'updated_at' => $this->updated_at,
        ];
    }
}
