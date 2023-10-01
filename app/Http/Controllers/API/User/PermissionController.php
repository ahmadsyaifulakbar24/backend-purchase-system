<?php

namespace App\Http\Controllers\API\User;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function get_role()
    {
        $role = Role::select('name')->get();
        
        return ResponseFormatter::success($role, 'success get role data');
    }

    public function create_role(Request $request)
    {
        $request->validate([
            'role' => ['required', 'string', 'unique:roles,name'],
        ]);

        $role = Role::create([
            'name' => $request->role, 
        ]);

        return ResponseFormatter::success($role, 'success create role data');
    }
}
