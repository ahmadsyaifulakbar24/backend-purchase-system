<?php

namespace App\Imports;

use App\Models\ItemCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemCategoryImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;
    
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)  
        {
            $category_code = $row['category_code'];
            $category = ItemCategory::where('category_code', $category_code)->first();

            if(empty($category)) {
                $parent_category_id = NUll;
                if(!empty($row['parent_category_code'])) {
                    $parent_category_id = ItemCategory::where('category_code', $row['parent_category_code'])->first()->id;
                }

                ItemCategory::create([
                    'category_code' => $category_code,
                    'category' => $row['category'],
                    'parent_category_id' => $parent_category_id,
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'category_code' => ['required'],
            'category' => ['required'],
            'parent_category_code' => ['nullable'],
        ];
    }
}
