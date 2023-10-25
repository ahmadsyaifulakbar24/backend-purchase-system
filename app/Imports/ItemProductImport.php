<?php

namespace App\Imports;

use App\Models\ItemCategory;
use App\Models\ItemProduct;
use App\Models\Param;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\Failure;

class ItemProductImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    use Importable;
    
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        $validationErrors = [];

        foreach ($rows as $index => $row) 
        {
            $validator = Validator::make($row->toArray(), [
                'code' => ['required', 'string', 'unique:item_products,code'],
                'name' => ['required', 'string'],
                'item_category_code' => [
                    'required', 
                    Rule::exists('item_categories', 'category_code')->where(function($query) {
                        $query->whereNull('parent_category_id');
                    })
                ],
                'sub_item_category_code' => [
                    'required', 
                    Rule::exists('item_categories', 'category_code')->where(function($query) use ($row) {
                        if(!empty($row['item_category_code'])) {
                            $item_category_code = ItemCategory::where('category_code', $row['item_category_code'])->first();
                            if(!empty($item_category_code)) {
                                $query->where('parent_category_id', $item_category_code->id);
                            }
                        }
                    })
                ],
                'brand' => ['nullable', 'string'],
                'description' => ['nullable', 'string'],
                'size' => ['required', 'string'],
                'unit' => [
                    'nullable', 
                    Rule::exists('params', 'param')->where(function ($query) {
                        $query->where('category', 'unit');
                    })
                ],
                'tax' => ['required', 'in:yes,no']
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                foreach ($errors as $fieldName => $errorMessages) {
                    foreach ($errorMessages as $errorMessage) {
                        $validationErrors[] = [
                            'field' => $fieldName,
                            'message' => 'There was an error on row ' . $index + 2 . '. ' . $errorMessage,
                            'row' => $index + 2,
                        ];
                    }
                }
            } else {
                $item_category_code_id = ItemCategory::where('category_code', $row['item_category_code'])->first()->id;
                $sub_item_category_code_id = ItemCategory::where('category_code', $row['sub_item_category_code'])->first()->id;
                $unit_id = Param::where('param', $row['unit'])->first()->id;

                ItemProduct::create([
                    'code' => $row['code'],
                    'name' => $row['name'],
                    'item_category_id' => $item_category_code_id,
                    'sub_item_category_id' => $sub_item_category_code_id,
                    'brand' => $row['brand'],
                    'description' => $row['description'],
                    'size' => $row['size'],
                    'unit_id' => $unit_id,
                    'tax' => $row['tax'],
                ]);
            }
        }

        if(!empty($validationErrors)) {
            foreach($validationErrors as $valError) {
                $error = [$valError['message']];
                $failures[] = new Failure($valError['row'], $valError['field'], $error);
            }
        
            throw new \Maatwebsite\Excel\Validators\ValidationException(\Illuminate\Validation\ValidationException::withMessages($error), $failures);
        }
        
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}