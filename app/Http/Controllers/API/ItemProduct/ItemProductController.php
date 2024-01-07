<?php

namespace App\Http\Controllers\API\ItemProduct;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemProduct\ItemProductResource;
use App\Imports\CategoryProductPriceImport;
use App\Imports\ItemProductImport;
use App\Models\ItemProduct;
use App\Models\Location;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ItemProductController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],
            'location_id' => ['nullable', 'exists:locations,id'],
        ]);
        $search = $request->search;
        $location_id = $request->location_id;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $item_product = ItemProduct::when($search, function ($query, string $search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('code', 'like', '%'. $search. '%')
                    ->orWhere('name', 'like', '%'. $search. '%');
            });
        })
        ->when($location_id, function ($query, $location_id) {
            $query->where('location_id', $location_id);
        })
        ->orderBy('code', 'ASC');
        
        $result = $paginate ? $item_product->paginate($limit) : $item_product->get();

        return ResponseFormatter::success(
            ItemProductResource::collection($result)->response()->getData(true),
            'success get item product data'
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'unique:item_products,code'],
            'name' => ['required', 'string'],
            'item_category_id' => [
                'required', 
                Rule::exists('item_categories', 'id')->where(function($query) {
                    $query->whereNull('parent_category_id');
                })
            ],
            'sub_item_category_id' => [
                'required', 
                Rule::exists('item_categories', 'id')->where(function($query) use ($request) {
                    $query->where('parent_category_id', $request->item_category_id);
                })
            ],
            'brand' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'size' => ['required', 'string'],
            'unit_id' => [
                'nullable', 
                Rule::exists('params', 'id')->where(function ($query) {
                    $query->where('category', 'unit');
                })
            ],
            'tax' => ['required', 'in:yes,no'],
            'location_id' => ['required', 'exists:locations,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'price' => ['required', 'numeric'],
            'sell_price' => ['required', 'numeric'],
        ]);

        
        $validator->after(function($validator) use ($request) {
            $location = Location::find($request->location_id);
            $supplier = Supplier::find($request->supplier_id);

            if($supplier->main == '1') {
                $item_product_check = ItemProduct::where([['location_id', $location->id], ['name', $request->name]])->count();
                if($item_product_check < 1) {
                    $validator->errors()->add(
                        'name', 'Product name not found on this supplier'
                    );
                }
            }

            if($location->main == '1') {
                if($supplier->main == '1') {
                    $validator->errors()->add(
                        'supplier_id', 'Suppliers cannot come from the center'
                    );
                }
            }    
        });
        $validator->validate();

        $input = $validator->safe()->all();
        $item_product = ItemProduct::create($input);

        return ResponseFormatter::success(
            new ItemProductResource($item_product),
            'success create item product data'
        );
    }

    public function import (Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx']
        ]);
        $file = $request->file;
        
        try {
            Excel::import(new ItemProductImport, $file);

            return ResponseFormatter::success(
                null,
                'success import item product data'
            );
        } catch (ValidationException $e) {
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $errors[] =  $failure->errors();
            }
            
            return ResponseFormatter::errorValidation(
                $errors,
                'import item product failed',
            );
        }

    }

    public function import_category_product_price(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx']
        ]);
        $file = $request->file;

        try {
            Excel::import(new CategoryProductPriceImport, $file);
            return ResponseFormatter::success(
                null,
                'success import category, product and price list data'
            );
        } catch (ValidationException $e) {
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $errors[] =  $failure->errors();
            }
            
            return ResponseFormatter::errorValidation(
                $errors,
                'import category, product and price list data failed',
            );
        }        
    }

    public function show(ItemProduct $item_product)
    {
        return ResponseFormatter::success(
            new ItemProductResource($item_product),
            'success show item product data'
        );
    }

    public function update(Request $request, ItemProduct $item_product)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'unique:item_products,code,' . $item_product->id],
            'name' => ['required', 'string'],
            'item_category_id' => [
                'required', 
                Rule::exists('item_categories', 'id')->where(function($query) {
                    $query->whereNull('parent_category_id');
                })
            ],
            'sub_item_category_id' => [
                'required', 
                Rule::exists('item_categories', 'id')->where(function($query) use ($request) {
                    $query->where('parent_category_id', $request->item_category_id);
                })
            ],
            'brand' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'size' => ['required', 'string'],
            'unit_id' => [
                'nullable', 
                Rule::exists('params', 'id')->where(function ($query) {
                    $query->where('category', 'unit');
                })
            ],
            'tax' => ['required', 'in:yes,no'],
            'location_id' => ['required', 'exists:locations,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'price' => ['required', 'numeric'],
            'sell_price' => ['required', 'numeric'],
        ]);
        $validator->after(function($validator) use ($request) {
            $location = Location::find($request->location_id);
            $supplier = Supplier::find($request->supplier_id);

            if($supplier->main == '1') {
                $item_product_check = ItemProduct::where([['location_id', $location->id], ['name', $request->name]])->count();
                if($item_product_check < 1) {
                    $validator->errors()->add(
                        'name', 'Product name not found on this supplier'
                    );
                }
            }

            if($location->main == '1') {
                if($supplier->main == '1') {
                    $validator->errors()->add(
                        'supplier_id', 'Suppliers cannot come from the center'
                    );
                }
            }    
        });
        $validator->validate();

        $input = $validator->safe()->all();
        $item_product->update($input);

        return ResponseFormatter::success(
            new ItemProductResource($item_product),
            'success update item product data'
        );
    }

    public function destroy(ItemProduct $item_product)
    {
        $item_product->delete();

        return ResponseFormatter::success(
            null,
            'success delete item product data'
        );
    }
}
