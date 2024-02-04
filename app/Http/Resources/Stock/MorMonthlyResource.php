<?php

namespace App\Http\Resources\Stock;

use App\Http\Resources\Param\ParamResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MorMonthlyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'item_category' => $this->item_category,
            'sub_item_category' => $this->sub_item_category,
            'brand' => $this->brand,
            'size' => $this->size,
            'unit' => new ParamResource($this->unit),
            'mor' => $this->mor,
        ];
    }
}
