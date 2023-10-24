<?php

namespace App\Http\Controllers\API\ItemProduct;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemProduct\ItemProductResource;
use App\Imports\ItemProductImport;
use App\Models\ItemProduct;
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
        ]);
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $item_product = ItemProduct::when($search, function ($query, string $search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('code', 'like', '%'. $search. '%')
                    ->orWhere('name', 'like', '%'. $search. '%');
            });
        })
        ->orderBy('name', 'ASC');
        
        $result = $paginate ? $item_product->paginate($limit) : $item_product->get();

        return ResponseFormatter::success(
            ItemProductResource::collection($result)->response()->getData(true),
            'success get item product data'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
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
            'tax' => ['required', 'in:yes,no']
        ]);

        $input = $request->all();
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

    public function show(ItemProduct $item_product)
    {
        return ResponseFormatter::success(
            new ItemProductResource($item_product),
            'success show item product data'
        );
    }

    public function update(Request $request, ItemProduct $item_product)
    {
        $request->validate([
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
            'tax' => ['required', 'in:yes,no']
        ]);

        $input = $request->all();
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
