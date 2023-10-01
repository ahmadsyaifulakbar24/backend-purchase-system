<?php

namespace App\Http\Controllers\API\Department;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Department\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
        ]);
        $search = $request->search;
        $limit = $request->input('limit', 10);

        $department = Department::when($search, function ($query, string $search) {
                                $query->where(function ($sub_query) use ($search) {
                                    $sub_query->where('department', 'like', '%'. $search. '%')
                                        ->orWhere('department_code', 'like', '%'. $search. '%');
                                });
                            })
                            ->orderBy('department', 'ASC')
                            ->paginate($limit);

        return ResponseFormatter::success(
            DepartmentResource::collection($department)->response()->getData(true),
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
