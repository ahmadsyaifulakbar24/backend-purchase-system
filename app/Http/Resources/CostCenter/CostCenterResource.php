<?php

namespace App\Http\Resources\CostCenter;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CostCenterResource extends JsonResource
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
            'cost_center_code' => $this->cost_center_code,
            'cost_center' => $this->cost_center,
        ];
    }
}
