<?php

    namespace App\Repository;

use App\Models\OrderHistory;
use Illuminate\Support\Facades\Auth;

class OrderHistoryRepository {

    public static function store(array $data)
    {
        $new_data = [
            'reference_id' => $data['reference_id'],
            'reference_type' => $data['reference_type'],
            'order_number' => $data['order_number'],
            'data' => $data['data'],
            'created_by' => Auth::user()->id,
        ];

        $order_history = OrderHistory::create($new_data);
        return $order_history;
    }


}