<?php

namespace App\Http\Resources\Quotation;

use App\Http\Resources\ItemProduct\ItemProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationItemProductResource extends JsonResource
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
            'weight' => $this->weight,
            'unit' => $this->unit,
            'quantity' => $this->quantity,
            'item_price' => $this->item_price,
            'vat' => $this->vat,
            'tnt' => $this->tnt,
            'remark' => $this->remark,
        ];
    }
}
