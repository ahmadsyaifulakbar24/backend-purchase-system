<?php

namespace App\Http\Controllers\API\Formula;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Formula\FormulaDetailResource;
use App\Http\Resources\Formula\FormulaResource;
use App\Models\Formula;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FormulaController extends Controller
{
    protected $timestamps;

    public function __construct()
    {
        $this->timestamps = Carbon::now()->format('Y_m_d_his');
    }
    
    public function index(Request $request)
    {
        $request->validate([
            'active' => ['nullable', 'in:yes,no'],
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:1,0'],
        ]);
        $active = $request->active;
        $search = $request->search;
        $paginate = $request->input('paginate', '1');
        $limit = $request->input('limit', 10);

        $formula = Formula::when($active, function ($query, $active) {
            $active_boolean = $active == 'yes' ? 1 : 0;
            $query->where('active', $active_boolean);
        })
        ->when($search, function($query, $search) {
            $query->where('title', 'like', '%'. $search .'%');
        });

        $result = $paginate ? $formula->paginate($limit) : $formula->get();

        return ResponseFormatter::success(
            FormulaResource::collection($result)->response()->getData(true),
            'success get formula data'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'unique:formulas,title', 'string'],
            'formula' => ['required'],
            'active' => ['required', 'boolean']
        ]);

        $formula = Formula::create([
            'title' => $request->title,
            'formula' => $request->formula,
            'active' => $request->active,
        ]);

        return ResponseFormatter::success(
            new FormulaDetailResource($formula),
            'success create formula data'
        );
    }

    public function show(Formula $formula)
    {
       return ResponseFormatter::success(
            new FormulaDetailResource($formula),
            'success show formula data'
       );
    }

    public function update(Request $request, Formula $formula)
    {
        $request->validate([
            'title' => [
                'required', 
                Rule::unique('formulas', 'title')->ignore($formula->id),
                'string'
            ],
            'formula' => ['required'],
            'active' => ['required', 'boolean']
        ]);

        $formula->update([
            'title' => $request->title,
            'formula' => $request->formula,
            'active' => $request->active,
        ]);

        return ResponseFormatter::success(
            new FormulaDetailResource($formula),
            'success update formula data'
        );
    }

    public function destroy(Formula $formula)
    {
        $formula->delete();

        return ResponseFormatter::success(
            null,
            'success delete formula data'
        );
    }

    public function testing_result(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string'],
            'formula' => ['required'],
            'total' => ['required', 'integer'],
            'accomodation' => ['required', 'integer']
        ]);

        $total = $request->total;
        $accomodation = $request->accomodation;
        $result = '';

        $data_validation = [
            'mandays',
            'casual'
        ];

        try {
            ob_start();
            eval("?>" . $request->formula);
            ob_get_clean();

            if(in_array($result, $data_validation)) {
                return ResponseFormatter::success([
                    'result' => $result,
                ], 'source code testing successful');
            } else {
                return ResponseFormatter::error([
                    'result' => 'invalid results',
                ], 'source code test failed');
            }
        } catch (\Throwable $e) {
            return ResponseFormatter::error([
                'result' => 'The source code you entered does not comply with the provisions',
                'details' => $e->getMessage(),
            ], 'source code test failed');
        }
    }
}
