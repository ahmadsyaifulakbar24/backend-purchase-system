<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductStockHistoryResource extends JsonResource
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
            'quantity' => $this->quantity,
            'from' => $this->from,
            'to' => $this->to,
            'purchase_order' => $this->purchase_order,
            'delivery_date' => $this->delivery_date,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
