<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CategoryProductPriceImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Item Category' => new ItemCategoryImport(),
            'Item Product' => new ItemProductImport(),
            'Price List' => new PriceListImport(),
        ];
    }
}
