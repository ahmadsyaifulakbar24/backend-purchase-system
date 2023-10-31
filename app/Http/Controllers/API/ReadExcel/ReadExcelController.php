<?php

namespace App\Http\Controllers\API\ReadExcel;

use App\Helpers\ExcelHelper;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemProduct\ItemProductResource;
use App\Models\ItemProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ReadExcelController extends Controller
{
    public function product_price_excel(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx'],
        ]);
        $file = $request->file;

        $data = ExcelHelper::read($file);

        $new_data = [];
        foreach ($data as $row) {
            $item_product = ItemProduct::where('code', $row['item_product_code'])->first();

            $row['item_product'] = new ItemProductResource($item_product);
            $new_data[] = Arr::except($row, ['item_product_code']);
        }

        return ResponseFormatter::success(
            $new_data,
            'success read data excel product price'
        );
    }
}
