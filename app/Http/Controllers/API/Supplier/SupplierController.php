<?php

namespace App\Http\Controllers\API\Supplier;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Supplier\SupplierDetailResource;
use App\Http\Resources\Supplier\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
        ]);
        $search = $request->search;
        $limit = $request->input('limit', 10);

        $supplier = Supplier::when($search, function ($query, string $search) {
                                $query->where(function ($sub_query) use ($search) {
                                    $sub_query->where('name', 'like', '%'. $search. '%')
                                        ->orWhere('code', 'like', '%'. $search. '%');
                                });
                            })
                            ->orderBy('created_at', 'DESC')
                            ->paginate($limit);

        return ResponseFormatter::success(
            SupplierResource::collection($supplier)->response()->getData(true),
            'success get supplier data'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'unique:suppliers,code'],
            'type' => ['required', 'string'],
            'name' => ['required', 'string'],
            'category' => ['required', 'string'],
            'npwp' => ['required', 'string'],
            'contact_person' => ['required', 'string'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'integer'],
        ]);

        $input = $request->all();
        $supplier = Supplier::create($input);

        return ResponseFormatter::success(
            new SupplierDetailResource($supplier),
            'success create supplier data'
        );
    }

    public function show(Supplier $supplier)
    {
        return ResponseFormatter::success(
            new SupplierDetailResource($supplier),
            'success show supplier data'
        );   
    }

    public function update(Supplier $supplier, Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'unique:suppliers,code,' . $supplier->id],
            'type' => ['required', 'string'],
            'name' => ['required', 'string'],
            'category' => ['required', 'string'],
            'npwp' => ['required', 'string'],
            'contact_person' => ['required', 'string'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'integer'],
        ]);

        $input = $request->all();
        $supplier->update($input);

        return ResponseFormatter::success(
            new SupplierDetailResource($supplier),
            'success update supplier data'
        );
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return ResponseFormatter::success(
            null,
            'success delete supplier data'
        );
    }
}
