<?php

namespace App\Repository;

use App\Models\ProductStock;

class ProductStockRepository {

    public static function find($data)
    {
        $item_product_id = $data['item_product_id'];
        $location_id = $data['location_id'];

        $product_stock = ProductStock::where([
            ['item_product_id', $item_product_id],
            ['location_id', $location_id],
        ])->first();

        return $product_stock;
    }

    public static function upsertProductStock ($data, ProductStock $product_stock = null)
    {
        $item_product_id = $data['item_product_id'];
        $location_id = $data['location_id'];
        $stock = $data['stock'];
        $quantity = $data['quantity'];
        $from = !empty($data['from']) ? $data['from'] : null;
        $to = !empty($data['to']) ? $data['to'] : null;
        $purchase_order = !empty($data['purchase_order']) ? $data['purchase_order'] : null;
        $delivery_date = !empty($data['delivery_date']) ? $data['delivery_date'] : null;
        $description = !empty($data['description']) ? $data['description'] : null;

        if(!empty($product_stock)) {
            $product_stock->update([
                'item_product_id' => $item_product_id,
                'location_id' => $location_id,
                'stock' => $stock,
            ]);
        } else {
            $product_stock = ProductStock::create([
                'item_product_id' => $item_product_id,
                'location_id' => $location_id,
                'stock' => $stock,
            ]);
        }

        $product_stock->product_stock_history()->create([
            'quantity' => $quantity,
            'from' => $from,
            'to' => $to,
            'purchase_order' => $purchase_order,
            'delivery_date' => $delivery_date,
            'description' => $description,
        ]);

        return $product_stock;
    }

}