<?php

namespace App\Http\Resources\Location;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $parent_location = null;
        if (!empty($this->parent_location)) {
            $parent_location = [
                'id' => $this->parent_location->id,
                'location_code' => $this->parent_location->location_code,
                'location' => $this->parent_location->location,
            ];
        }

        return [
            'id' => $this->id,
            'location_code' => $this->location_code,
            'location' => $this->location,
            'parent_location' => $parent_location,
            'main' => $this->main,
        ];
    }
}
