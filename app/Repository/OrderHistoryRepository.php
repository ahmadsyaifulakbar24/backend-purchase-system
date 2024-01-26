<?php

    namespace App\Repository;

use App\Models\OrderHistory;

class OrderHistoryRepository {

    public static function store(array $data)
    {
        $new_data = [
            'reference_id' => $data['reference_id'],
            'reference_type' => $data['reference_type'],
            'order_number' => $data['order_number'],
            'data' => $data['data'],
        ];

        $order_history = OrderHistory::create($new_data);
        return $order_history;
    }


}