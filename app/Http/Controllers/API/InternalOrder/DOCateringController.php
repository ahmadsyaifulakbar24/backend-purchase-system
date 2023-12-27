<?php

namespace App\Http\Controllers\API\InternalOrder;

use App\Helpers\DateHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\InternalOrder\DOCateringRequest;
use App\Http\Resources\InternalOrder\DOCatering\DOCateringDetailResource;
use App\Http\Resources\InternalOrder\DOCatering\DOCateringResource;
use App\Models\DOCatering;
use App\Models\Location;
use App\Models\SelectItemProduct;
use App\Repository\ProductStockRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DOCateringController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],
            'status' => ['nullable', 'in:draft,submit'],
        ]);
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);
        $status = $request->status;

        $do_catering = DOCatering::when($search, function ($query, string $search) {
                                    $query->where(function ($query2) use ($search) {
                                        $query2->where('do_number', 'like', '%'.$search.'%')
                                        ->orWhereHas('po_supplier_catering', function ($sub_query) use ($search) {
                                            $sub_query->where('po_number', 'like', '%'.$search.'%');
                                        });
                                    });
                                })
                                ->when($status, function ($query, string $status) {
                                    $query->where('status', $status);
                                })
                                ->orderBy('created_at', 'DESC');

        $result = $paginate ? $do_catering->paginate($limit) : $do_catering->get();

        return ResponseFormatter::success(
            DOCateringResource::collection($result)->response()->getData(true),
            'success get do catering data'
        );
    }  

    public function show(DOCatering $do_catering)
    {
        return ResponseFormatter::success(
            new DOCateringDetailResource($do_catering),
            'success show do catering detail data'
        );
    }    

    public function update(DOCateringRequest $request, DOCatering $do_catering)
    {
        if($do_catering->status == 'submit')
        {
            return ResponseFormatter::errorValidation([
                'do_catering_id' => ['cannot update this data because the status has already been submitted']
            ], 'update do cateirng data failed', 422);
        }

        // database transaction for do catering and item data
        $result = DB::transaction(function () use ($request, $do_catering) {
            // delete do catering item product
            $do_catering->item_product()->delete();

            // store item product data
            foreach($request->item_product as $item_product) {
                $item_product['reference_type'] = 'App\Models\DOCatering';
                $item_product['reference_id'] = $do_catering->id;
                SelectItemProduct::create($item_product);
            }

            return $do_catering;
        });

        return ResponseFormatter::success(
            new DOCateringDetailResource($result),
            'success update do catering data'
        );
    }

    public function update_status(Request $request, DOCatering $do_catering)
    {
        $request->validate([
            'status' => ['required', 'in:draft,submit'],
        ]);
        $status = $request->status;

        try {
            DB::beginTransaction();

            if(($do_catering->status == 'submit' && $status == 'submit') || ($do_catering->status == 'draft' && $status == 'draft')) {
                return ResponseFormatter::errorValidation([
                    'do_catering_id' => 'status is the same',
                ], 'update status do catering failed');
            }

            if ($status == 'submit') {
                // add product stock berdasarkan lokasi pr catering
                $this->update_to_stock($do_catering, 'plus');
            } else if ($status == 'draft') {
                // rollback product stock berdasarkan lokasi pr catering
                $this->update_to_stock($do_catering, 'minus');
            }

            $do_catering->update([
                'status' => $status
            ]);
            
            DB::commit();
            return ResponseFormatter::success(
                new DOCateringDetailResource($do_catering),
                'success update status do catering data'
            );
        } catch (\Exception $e) {
            DB::rollBack();
        
            return ResponseFormatter::errorValidation([
                'error_message' => [$e->getMessage()],
            ], 'update status do catering failed');
        }
    }

    public function update_to_stock(DOCatering $do_catering, $type = 'plus')
    {
        $item_products = $do_catering->item_product;
        $location_id = $do_catering->po_supplier_catering->po_catering->pr_catering->location_id;
        $supplier = $do_catering->po_supplier_catering->supplier;
        $from = $supplier->name;
        $to = $do_catering->po_supplier_catering->po_catering->pr_catering->location->location;
        $purchase_order = $do_catering->po_supplier_catering->po_catering->po_number;
        $delivery_date = $do_catering->po_supplier_catering->po_catering->pr_catering->delivery_date;

        // update product berdasarkan lokasi 
            foreach ($item_products as $item_product) {
                $quantity = $type == 'minus' ? intval(-$item_product['quantity']) : $item_product['quantity'];
                $message = $type == 'minus' ? 'Rollback stock product from do catering' : 'Added product from DO Catering';

                $data = [
                    'item_product_id' => $item_product['item_product_id'],
                    'location_id' => $location_id,
                    'quantity' => $quantity,
                    'from' => $from,
                    'purchase_order' => $purchase_order,
                    'delivery_date' => $delivery_date,
                    'description'  => '-',
                ];

                // perhitungan stock
                $product_stock = ProductStockRepository::find($data);
                if(!empty($product_stock)) {
                    $data['stock'] = $product_stock->stock + $quantity;
                } else {
                    $data['stock'] = $quantity;
                }
                ProductStockRepository::upsertProductStock($data, $product_stock);
            }
        // end update product berdasarkan lokasi 

        // kurangi product pusat jika supplier adalah pusat
            if ($supplier->main == '1') {
                $pusat_location = Location::where('main', '1')->first();

                foreach ($item_products as $item_product_sup) {
                    $quantity_sup = $type == 'minus' ? $item_product_sup['quantity'] : intval(-$item_product_sup['quantity']);
                    $message_sup = $type == 'minus' ? 'Rollback stock product from do catering' : 'Product reduction from DO Catering';
    
                    $data_sup = [
                        'item_product_id' => $item_product_sup['item_product_id'],
                        'location_id' => $pusat_location->id,
                        'to' => $to,
                        'purchase_order' => $purchase_order,
                        'delivery_date' => $delivery_date,
                        'quantity' => $quantity_sup,
                        'description'  => '-',
                    ];
    
                    // perhitungan stock
                    $product_stock_sup = ProductStockRepository::find($data_sup);
                    if(!empty($product_stock_sup)) {
                        $data_sup['stock'] = $product_stock_sup->stock + $quantity_sup;
                    } else {
                        $data_sup['stock'] = $quantity_sup;
                    }
                    ProductStockRepository::upsertProductStock($data_sup, $product_stock_sup);
                }
            }
        // end
    }

}
