<?php

namespace App\Http\Resources\PurchaseRequest;

use App\Http\Resources\ItemProduct\ItemProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseRequestItemProductResource extends JsonResource
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
            'item_name' => $this->item_name,
            'item_brand' => $this->item_brand,
            'description' => $this->description,
            'size' => $this->size,
            'unit' => $this->unit,
            'item_price' => $this->item_price,
            'quantity' => $this->quantity,
            'vat' => $this->vat,
            'remark' => $this->remark,
        ];
    }
}
