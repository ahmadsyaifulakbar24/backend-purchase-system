<?php

namespace App\Http\Controllers\API\Customer;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CustomerResource;
use App\Imports\CustomerImport;
use App\Models\Customer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1']
        ]);
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $customer = Customer::when($search, function ($query, string $search) {
                                $query->where(function ($sub_query) use ($search) {
                                    $sub_query->where('name', 'like', '%'. $search. '%')
                                        ->orWhere('code', 'like', '%'. $search. '%');
                                });
                            })
                            ->orderBy('code', 'DESC');
        $result = $paginate ? $customer->paginate($limit) : $customer->get();

        return ResponseFormatter::success(
            CustomerResource::collection($result)->response()->getData(true),
            'success get customer data'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'unique:customers,code'],
            'name' => ['required', 'string'],
            'contact_person' => ['required', 'string'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'numeric'],
        ]);

        $input = $request->all();
        $customer = Customer::create($input);

        return ResponseFormatter::success(
            new CustomerResource($customer),
            'success create customer data'
        );
    }

    public function import (Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx']
        ]);
        $file = $request->file;

        Excel::import(new CustomerImport, $file);

        return ResponseFormatter::success(
            null,
            'success import customer data'
        );
    }

    public function show(Customer $customer)
    {
        return ResponseFormatter::success(
            new CustomerResource($customer),
            'success show customer data'
        );   
    }

    public function update(Customer $customer, Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'unique:customers,code,' . $customer->id],
            'name' => ['required', 'string'],
            'contact_person' => ['required', 'string'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'numeric'],
        ]);

        $input = $request->all();
        $customer->update($input);

        return ResponseFormatter::success(
            new CustomerResource($customer),
            'success update customer data'
        );
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return ResponseFormatter::success(
            null,
            'success delete customer data'
        );
    }
}
