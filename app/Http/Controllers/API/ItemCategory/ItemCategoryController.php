<?php

namespace App\Http\Controllers\API\ItemCategory;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemCategory\ItemCategoryResource;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemCategoryController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],
            'only_parent' => ['nullable', 'in:0,1'],
            'parent_category_id' => [
                'nullable',
                Rule::exists('item_categories', 'id')->where(function($query) {
                    $query->whereNull('parent_category_id');
                })
            ]
        ]);
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);
        $only_parent = $request->only_parent;
        $parent_category_id = $request->parent_category_id;

        $item_category = ItemCategory::when($search, function ($query, string $search) {
                                $query->where(function ($sub_query) use ($search) {
                                    $sub_query->where('category', 'like', '%'. $search. '%')
                                        ->orWhere('category_code', 'like', '%'. $search. '%');
                                });
                            })
                            ->when($parent_category_id, function($query, string $parent_category_id) {
                                $query->where('parent_category_id', $parent_category_id);
                            })
                            ->when($only_parent, function($query) {
                                $query->whereNull('parent_category_id');
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
