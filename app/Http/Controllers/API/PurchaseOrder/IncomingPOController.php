<?php

namespace App\Http\Controllers\API\PurchaseOrder;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrder\IncomingPORequest;
use App\Http\Resources\PurchaseOrder\IncomingPO\IncomingPODetailResource;
use App\Http\Resources\PurchaseOrder\IncomingPO\IncomingPOResource;
use App\Models\IncomingPo;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IncomingPOController extends Controller
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

        $incoming_po = IncomingPo::when($search, function ($query, string $search) {
                                    $query->where('po_number', $search);
                                })
                                ->orderBy('created_at', 'DESC');

        $result = $paginate ? $incoming_po->paginate($limit) : $incoming_po->get();

        return ResponseFormatter::success(
            IncomingPOResource::collection($result)->response()->getData(true),
            'success get incoming po data'
        );
    }

    public function store(IncomingPORequest $request)
    {
        $input = $request->validated();
        $incoming_po = IncomingPo::create($input);
        return ResponseFormatter::success(
            new IncomingPOResource($incoming_po),
            'success create incoming po data'
        );
    }

    public function show(IncomingPo $incoming_po)
    {
        return ResponseFormatter::success(
            new IncomingPODetailResource($incoming_po),
            'success show incoming po data'
        );
    }

    public function update(IncomingPORequest $request, IncomingPo $incoming_po)
    {
        $input = $request->validated();

        $incoming_po->update($input);
        return ResponseFormatter::success(
            new IncomingPOResource($incoming_po),
            'success update incoming po data'
        );
    }

    public function destroy(IncomingPo $incoming_po)
    {
        
        DB::transaction(function () use ($incoming_po) {
            // delete attachment file 
            $files = $incoming_po->attachment_file()->pluck('file')->toArray();
            Storage::disk('local')->delete($files);    
            $incoming_po->attachment_file()->delete();

            // delete incoming data
            $incoming_po->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete incoming po data'
        );
    }
}
