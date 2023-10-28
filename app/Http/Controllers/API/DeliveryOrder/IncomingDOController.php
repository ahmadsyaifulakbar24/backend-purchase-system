<?php

namespace App\Http\Controllers\API\DeliveryOrder;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryOrder\IncomingDORequest;
use App\Http\Resources\DeliveryOrder\IncomingDO\IncomingDODetailResource;
use App\Http\Resources\DeliveryOrder\IncomingDO\IncomingDOResource;
use App\Models\IncomingDo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IncomingDOController extends Controller
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

        $incoming_do = IncomingDo::when($search, function ($query, string $search) {
                                    $query->where('do_number', 'like', '%'.$search.'%');
                                })
                                ->orderBy('created_at', 'DESC');

        $result = $paginate ? $incoming_do->paginate($limit) : $incoming_do->get();

        return ResponseFormatter::success(
            IncomingDOResource::collection($result)->response()->getData(true),
            'success get incoming do data'
        );
    }

    public function store(IncomingDORequest $request)
    {
        $input = $request->validated();
        $incoming_do = IncomingDo::create($input);
        return ResponseFormatter::success(
            new IncomingDOResource($incoming_do),
            'success create incoming do data'
        );
    }

    public function show(IncomingDo $incoming_do)
    {
        return ResponseFormatter::success(
            new IncomingDODetailResource($incoming_do),
            'success show incoming do data'
        );
    }

    public function update(IncomingDORequest $request, IncomingDo $incoming_do)
    {
        $input = $request->validated();

        $incoming_do->update($input);
        return ResponseFormatter::success(
            new IncomingDODetailResource($incoming_do),
            'success update incoming do data'
        );
    }

    public function destroy(IncomingDo $incoming_do)
    {
        DB::transaction(function () use ($incoming_do) {
            // delete attachment file 
            $files = $incoming_do->attachment_file()->pluck('file')->toArray();
            Storage::disk('local')->delete($files);    
            $incoming_do->attachment_file()->delete();

            // delete incoming data
            $incoming_do->delete();
        });

        return ResponseFormatter::success(
            null,
            'success delete incoming do data'
        );
    }
}
