<?php

namespace App\Http\Resources\SelectItemProduct;

use App\Http\Resources\ItemProduct\ItemProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SelectItemProductResource extends JsonResource
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
            'description' => $this->description,
            'weight' => $this->weight,
            'quantity' => $this->quantity,
            'item_price' => $this->item_price,
            'vat' => $this->vat,
            'tnt' => $this->tnt,
            'remark' => $this->remark,
        ];
    }
}
