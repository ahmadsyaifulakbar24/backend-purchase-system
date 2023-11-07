<?php

namespace App\Http\Resources\MealSheet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealSheetClientResource extends JsonResource
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
            'client_name' => $this->client_name
        ];
    }
}
