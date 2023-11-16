<?php

namespace App\Http\Controllers\API\Department;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Department\DepartmentResource;
use App\Imports\DepartmentImport;
use App\Models\Department;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DepartmentController extends Controller
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

        $department = Department::when($search, function ($query, string $search) {
                                $query->where(function ($sub_query) use ($search) {
                                    $sub_query->where('department', 'like', '%'. $search. '%')
                                        ->orWhere('department_code', 'like', '%'. $search. '%');
                                });
                            })
                            ->orderBy('department_code', 'ASC');
                            
        $result = $paginate ? $department->paginate($limit) : $department->get();

        return ResponseFormatter::success(
            DepartmentResource::collection($result)->response()->getData(true),
            'success get department data'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_code' => ['required', 'unique:departments,department_code'],
            'department' => ['required', 'unique:departments,department']
        ]);

        $input = $request->all();
        $department = Department::create($input);

        return ResponseFormatter::success(
            new DepartmentResource($department),
            'success create department data'
        );
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx'],
        ]);
        $file = $request->file;

        Excel::import(new DepartmentImport, $file);

        return ResponseFormatter::success(
            null,
            'success import department data'
        );
    }

    public function show(Department $department)
    {
        return ResponseFormatter::success(
            new DepartmentResource($department),
            'success show department data'
        );
    }

    public function update(Department $department, Request $request)
    {
        $request->validate([
            'department_code' => ['required', 'unique:departments,department_code,'. $department->id],
            'department' => ['required', 'unique:departments,department,'. $department->id]
        ]);

        $input = $request->all();
        $department->update($input);

        return ResponseFormatter::success(
            new DepartmentResource($department),
            'success update department data'
        );
    }

    public function destroy(Department $department)
    {
        
        $department->delete();

        return ResponseFormatter::success(
            null,
            'success delete department data'
        );
    }
}
