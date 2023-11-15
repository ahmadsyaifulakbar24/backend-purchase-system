<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\Location\LocationResource;
use App\Http\Resources\Param\ParamResource;
use App\Models\Param;
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
                'id' => $this->item_product_id,
                'code' => $this->code,
                'name' => $this->name,
                'brand' => $this->brand,
                'size' => $this->size,
                'unit' => new ParamResource(Param::find($this->unit_id)),
                'price' => $this->price,
            ],
            'stock' => !empty($this->stock) ? $this->stock : 0,
            'updated_at' => $this->updated_at,
        ];
    }
}
