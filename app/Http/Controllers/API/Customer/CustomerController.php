<?php

namespace App\Http\Controllers\API\Customer;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
        ]);
        $search = $request->search;
        $limit = $request->input('limit', 10);

        $customer = Customer::when($search, function ($query, string $search) {
                                $query->where(function ($sub_query) use ($search) {
                                    $sub_query->where('name', 'like', '%'. $search. '%')
                                        ->orWhere('code', 'like', '%'. $search. '%');
                                });
                            })
                            ->orderBy('created_at', 'DESC')
                            ->paginate($limit);

        return ResponseFormatter::success(
            CustomerResource::collection($customer)->response()->getData(true),
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
            'phone' => ['required', 'integer'],
        ]);

        $input = $request->all();
        $customer = Customer::create($input);

        return ResponseFormatter::success(
            new CustomerResource($customer),
            'success create customer data'
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
            'phone' => ['required', 'integer'],
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
