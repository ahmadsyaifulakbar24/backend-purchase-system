<?php

namespace App\Imports;

use App\Models\Discount;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DiscountImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            Discount::create([
                'discount' => $row['discount'],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'discount' => ['required', 'unique:discounts,discount', 'integer', 'max:100', 'distinct'],
        ];
    }
}
