<?php

namespace App\Http\Resources\Param;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $parent = null;
        if($this->parent) {
            $parent = [
                'id' => $this->parent->id,
                'param' => $this->parent->param,    
            ];
        }

        return [
            'id' => $this->id,
            'parent' => $parent,
            'param' => $this->param,
        ];
    }
}
