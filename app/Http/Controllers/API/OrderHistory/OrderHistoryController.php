<?php

namespace App\Http\Controllers\API\OrderHistory;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderHistory\OrderHistoryDetailResource;
use App\Http\Resources\OrderHistory\OrderHistoryResource;
use App\Models\OrderHistory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderHistoryController extends Controller
{

    protected $reference_type_data, $reference_type;

    public function __construct()
    {
        $this->reference_type = [
            'pr_catering',
            'pr_customer',
        ];

        $this->reference_type_data = [
            'pr_catering' => 'App\Models\PRCatering',
            'pr_customer' => 'App\Models\PRCustomer',
        ];
    }

    public function get(Request $request)
    {
        $request->validate([
            'reference_type' => [
                'required', 
                Rule::in($this->reference_type)
            ],
            'reference_id' => ['required'],
        ]);
        $reference_type = $this->reference_type_data[$request->reference_type];
        $reference_id = $request->reference_id;

        $order_history = OrderHistory::where([
            ['reference_type', $reference_type],
            ['reference_id', $reference_id],
        ])
        ->orderBy('updated_at', 'DESC')
        ->get();

        return ResponseFormatter::success(
            OrderHistoryResource::collection($order_history),
            'success get order history data'
        );
    }

    public function show(OrderHistory $order_history)
    {   
        return ResponseFormatter::success(
            new OrderHistoryDetailResource($order_history),
            'success show order history data',
        );
    }
}
