<?php

namespace App\Http\Controllers\API\Supplier;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Supplier\SupplierDetailResource;
use App\Http\Resources\Supplier\SupplierResource;
use App\Imports\SupplierImport;
use App\Models\POCatering;
use App\Models\POCustomer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'po_catering_id' => ['nullable', 'exists:po_caterings,id'],
            'po_customer_id' => ['nullable', 'exists:po_customers,id'],
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],
        ]);
        $po_catering_id = $request->po_catering_id;
        $po_customer_id = $request->po_customer_id;
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $po_catering_supplier = collect();
        $po_customer_supplier = collect();

        $supplier = Supplier::when($search, function ($query, string $search) {
                                $query->where(function ($sub_query) use ($search) {
                                    $sub_query->where('name', 'like', '%'. $search. '%')
                                        ->orWhere('code', 'like', '%'. $search. '%');
                                });
                            });

        if(!empty($po_catering_id)) {
            $po_catering = POCatering::find($po_catering_id);
            $po_catering_supplier = $po_catering->join_item_product()->groupBy('supplier_id')->pluck('supplier_id');
        }

        if(!empty($po_customer_id)) {
            $po_customer = POCustomer::find($po_customer_id);
            $po_customer_supplier = $po_customer->join_item_product()->groupBy('supplier_id')->pluck('supplier_id');
        }

        $supplier_id_arr = $po_catering_supplier->merge($po_customer_supplier);
        if($supplier_id_arr->isNotEmpty()) {
            $supplier->whereIn('id', $supplier_id_arr);
        }
        
        $supplier->orderBy('code', 'DESC');
            
        $result = $paginate ? $supplier->paginate($limit) : $supplier->get();

        return ResponseFormatter::success(
            SupplierDetailResource::collection($result)->response()->getData(true),
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
            'phone' => ['required', 'numeric'],
        ]);

        $input = $request->all();
        $supplier = Supplier::create($input);

        return ResponseFormatter::success(
            new SupplierDetailResource($supplier),
            'success create supplier data'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx'],
        ]);
        $file = $request->file;

        Excel::import(new SupplierImport, $file);

        return ResponseFormatter::success(
            null,
            'success import supplier data'
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
            'phone' => ['required', 'numeric'],
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
