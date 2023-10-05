<?php

namespace App\Http\Resources\ItemCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $parent_cetegory = null;
        if (!empty($this->parent_category)) {
            $parent_cetegory = [
                'id' => $this->parent_category->id,
                'category_code' => $this->parent_category->category_code,
                'category' => $this->parent_category->category,
            ];
        }

        return [
            'id' => $this->id,
            'category_code' => $this->category_code,
            'category' => $this->category,
            'parent_category' => $parent_cetegory ,
        ];
    }
}
