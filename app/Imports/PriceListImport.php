<?php

namespace App\Imports;

use App\Models\ItemProduct;
use App\Models\Location;
use App\Models\PriceList;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class PriceListImport implements ToCollection, WithHeadingRow, WithValidation
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
            $location_id = Location::where('location_code', $row['location_code'])->first()->id;
            $supplier_id = Supplier::where('code', $row['supplier_code'])->first()->id;
            $item_product_id = ItemProduct::where('code', $row['item_product_code'])->first()->id;

            $price_check = PriceList::where([
                ['location_id', $location_id],
                ['supplier_id', $supplier_id],
                ['item_product_id', $item_product_id],
            ])->count();

            if($price_check < 1) {
                PriceList::create([
                    'location_id' => $location_id,
                    'supplier_id' => $supplier_id,
                    'item_product_id' => $item_product_id,
                    'price' => $row['price'],
                ]);
            } else {
                $validationErrors[] = [
                    'field' => 'price',
                    'message' => 'There was an error on row ' . $index + 2 . '. ' . 'prices with this data already exist',
                    'row' => $index + 2,
                ];
            }
        }

        Log::error($validationErrors);
        if(!empty($validationErrors)) {
            foreach($validationErrors as $valError) {
                $error = [$valError['message']];
                $failures[] = new Failure($valError['row'], $valError['field'], $error);
            }
        
            throw new \Maatwebsite\Excel\Validators\ValidationException(\Illuminate\Validation\ValidationException::withMessages($error), $failures);
        }
    }

    public function rules(): array
    {
        return [
            'location_code' => ['required', 'exists:locations,location_code'],
            'supplier_code' => ['required', 'exists:suppliers,code'],
            'item_product_code' => ['required', 'exists:item_products,code'],
            'price' => ['required', 'numeric'],
        ];
    }
}
