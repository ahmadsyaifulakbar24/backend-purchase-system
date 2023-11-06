<?php

namespace App\Http\Controllers\API\Client;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\ClientResource;
use App\Imports\ClientImport;
use App\Models\Client;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],
        ]);
        $search = $request->search;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);

        $client = Client::when($search, function ($query, string $search) {
                    $query->where(function ($sub_query) use ($search) {
                        $sub_query->where('client_name', 'like', '%'. $search. '%');
                    });
                })
                ->orderBy('client_name', 'ASC');
                
        $result = $paginate ? $client->paginate($limit) : $client->get();

        return ResponseFormatter::success(
        ClientResource::collection($result)->response()->getData(true),
        'success get client data'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => ['required', 'string', 'unique:clients,client_name'],
        ]);

        $input = $request->only(['client_name']);
        $client = Client::create($input);

        return ResponseFormatter::success(
            new ClientResource($client),
            'success create client data'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx'],
        ]);
        $file = $request->file;

        Excel::import(new ClientImport, $file);

        return ResponseFormatter::success(
            null,
            'success import client data'
        );
    }

    public function show(Client $client)
    {
        return ResponseFormatter::success(
            new ClientResource($client),
            'success show client data'
        );
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'client_name' => ['required', 'string', 'unique:clients,client_name,' . $client->id],
        ]);

        $input = $request->only(['client_name']);
        $client->update($input);

        return ResponseFormatter::success(
            new ClientResource($client),
            'success update client data'
        );
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return ResponseFormatter::success(
            null,
            'success delete client data'
        );
    }
}
