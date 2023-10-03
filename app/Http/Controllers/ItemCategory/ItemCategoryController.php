<?php

namespace App\Http\Controllers\ItemCategory;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemCategory\ItemCategoryResource;
use App\Models\ItemCategory;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],
        ]);
        $search = $request->search;
        $paginate = $request->paginate;
        $limit = $request->input('limit', 10);

        $item_category = ItemCategory::when($search, function ($query, string $search) {
                                $query->where(function ($sub_query) use ($search) {
                                    $sub_query->where('category', 'like', '%'. $search. '%')
                                        ->orWhere('category_code', 'like', '%'. $search. '%');
                                });
                            })
                            ->orderBy('category', 'ASC');
                        
        $result = $paginate ? $item_category->paginate($limit) : $item_category->get();

        return ResponseFormatter::success(
            ItemCategoryResource::collection($result)->response()->getData(true),
            'success get item category data'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_code' => ['required', 'unique:item_categories,category_code'],
            'category' => ['required', 'unique:item_categories,category'],
            'parent_category_id' => ['nullable', 'exists:item_categories,id'],
        ]);

        $input = $request->all();
        $item_category = ItemCategory::create($input);

        return ResponseFormatter::success(
            new ItemCategoryResource($item_category),
            'success create item category data'
        );
    }

    public function show(ItemCategory $item_category)
    {
        return ResponseFormatter::success(
            new ItemCategoryResource($item_category),
            'success show item category data'
        );
    }
    
    public function update(ItemCategory $item_category, Request $request)
    {
        $request->validate([
            'category_code' => ['required', 'unique:item_categories,category_code,' . $item_category->id],
            'category' => ['required', 'unique:item_categories,category,' . $item_category->id],
            'parent_category_id' => ['nullable', 'exists:item_categories,id'],
        ]);

        $input = $request->all();
        $item_category->update($input);

        return ResponseFormatter::success(
            new ItemCategoryResource($item_category),
            'success update item category data'
        );
    }

    public function destroy(ItemCategory $item_category)
    {
        $item_category->delete();
     
        return ResponseFormatter::success(
            null,
            'success delete item category data'
        );
    }
}
