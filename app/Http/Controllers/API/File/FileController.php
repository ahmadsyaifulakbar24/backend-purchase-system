<?php

namespace App\Http\Controllers\API\File;

use App\Helpers\FileHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\File\FileResource;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FileController extends Controller
{
    protected 
        $reference_type, 
        $type,
        $reference_alias;

    public function __construct()
    {
        $this->reference_alias = [
            'pr_catering',
            'po_catering',
            'po_supplier_catering',
            'do_catering',

            'pr_customer',
            'quotation',
            'po_customer',
            'po_supplier_customer',
            'do_customer'
        ];

        $this->reference_type = [
            'pr_catering' => 'App\Models\PRCatering',
            'po_catering' => 'App\Models\POCatering',
            'po_supplier_catering' => 'App\Models\POSupplierCatering',
            'do_catering' => 'App\Models\DOCatering',

            'pr_customer' => 'App\Models\PRCustomer',
            'quotation' => 'App\Models\Quotation',
            'po_customer' => 'App\Models\POCustomer',
            'po_supplier_customer' => 'App\Models\POSupplierCustomer',
            'do_customer' => 'App\Models\DOCustomer',
        ];

        $this->type = [
            'attachment'
        ];
    }

    public function get_file(Request $request)
    {
        $request->validate([
            'reference_type' => [
                'required',
                Rule::in($this->reference_alias)
            ],
           'reference_id' => ['nullable', 'string'],
           'type' => [
                'nullable',
                Rule::in($this->type),
            ],
           'paginate' => ['nullable', 'boolean'],
           'limit' => ['nullable', 'integer']
        ]);

        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);
        $reference_type = $this->reference_type[$request->reference_type];
        $type = $request->type;
        $reference_id = $request->reference_id;

        $file = File::where('reference_type', $reference_type)
                    ->when($type, function ($query, $type) {
                        $query->where('type', $type);
                    })
                    ->when($reference_id, function ($query, $reference_id) {
                        $query->where('reference_id', $reference_id);
                    });

        $result = $paginate ? $file->paginate($limit) : $file->get();

        return ResponseFormatter::success(
            FileResource::collection($result)->response()->getData(true), 
            'success get file data'
        );


    }

    public function store(Request $request)
    {
        $request->validate([
            'reference_type' => [
                'required',
                Rule::in($this->reference_alias)
            ],
            'reference_id' => ['required', 'string'],
            'file' => ['required', 'file'],
            'type' => ['required', Rule::in($this->type)]
        ]);

        $file = $request->file('file');
        $type = $request->type;
        $reference_type = $this->reference_type[$request->reference_type];
        $reference_id = $request->reference_id;

        $path = FileHelpers::upload_file($request->reference_type .'/'. $type , $file, 'local', false);
        $file_data = File::create([
            'reference_type' => $reference_type,
            'reference_id' => $reference_id,
            'type' => $type,
            'original_file_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'file' => $path
        ]);

        return ResponseFormatter::success(
            new FileResource($file_data), 
            "success uplaod file $type"
        );
    }

    public function show_file(Request $request)
    {
        $request->validate([
            'path' => ['required', 'string']
        ]);

        $path = $request->path;
        if(Storage::disk('local')->exists($path)) {
            $filename = basename($path);
            $file = Storage::disk('local')->get($path);
            
            return response($file, 200)
                ->withHeaders([
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            
        } else {
            return ResponseFormatter::error('file not found', 404);
        }
    }

    public function destroy(File $file)
    {
        if(!empty($file->file)) {
            if (Storage::disk('local')->exists($file->file)) {
                Storage::disk('local')->delete($file->file);
            }
        }

        $file->delete();

        return ResponseFormatter::success(null, 'success delete file data');
    }
}
