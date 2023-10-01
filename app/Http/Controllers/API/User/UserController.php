<?php

namespace App\Http\Controllers\API\User;

use App\Helpers\FileHelpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserDetailResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'role' => ['nullable', 'exists:roles,name'],

            'status' => ['nullable', 'array'],
            'status.*' => ['nullable', 'in:active,not_active', 'distinct'],

            'department_id' => ['nullable', 'array'],
            'department_id.*' => ['nullable', 'exists:departments,id', 'distinct'],

            'location_id' => ['nullable', 'array'],
            'location_id.*' => ['nullable', 'exists:locations,id', 'distinct'],

            'limit' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
        ]);

        $role = $request->role;
        $status = $request->status;
        $department_id = $request->department_id;
        $location_id = $request->location_id;
        $limit = $request->input('limit', 10);
        $search = $request->search;

        $user = User::when($role, function ($query, $role) {
            $query->whereHas('roles', function ($sub_query) use ($role) {
                $sub_query->where('name', $role);
            });
        })
        ->when($status, function ($query, array $status) {
            $query->whereIn('status', $status);
        })
        ->when($department_id, function ($query, array $department_id) {
            $query->whereIn('department_id', $department_id);
        })
        ->when($location_id, function ($query, array $location_id) {
            $query->whereIn('location_id', $location_id);
        })
        ->when($search, function ($query, $search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('code', 'like', '%'. $search .'%')
                        ->orWhere('username', 'like', '%'. $search .'%')
                        ->orWhere('name', 'like', '%'. $search .'%');
            });
        })
        ->paginate($limit);

        return ResponseFormatter::success(
            UserResource::collection($user)->response()->getData(),
            'success get user data'
        );
    }

    public function store(Request $request) 
    {
        $request->validate([
            'code' => ['required', 'string', 'unique:users,code'],
            'name' => ['required', 'string'],
            'username' => ['required', 'string', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'password_confirmation' => ['required', 'min:8'],
            'status' => ['required', 'in:active,not_active'],
            'photo' => ['nullable', 'image', 'mimes:png,jpg,jpeg'],
            'department_id' => ['required', 'exists:departments,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'address' => ['nullable', 'string'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $result = DB::transaction(function () use ($request) {
            $input = $request->except([
                'password',
                'photo',
                'role'
            ]);
            $input['password'] = Hash::make($request->password);
            
            // upload file photo if exists
            if(!empty($request->file('photo'))) {
                $input['photo'] = FileHelpers::upload_file(
                    'user/photo-profile', 
                    $request->file('photo'), 
                    'local'
                );
            }
    
            $user = User::create($input);
            $user->assignRole($request->role);

            return $user;
        });

        return ResponseFormatter::success(
            new UserDetailResource($result),
            'success create user data'
        );
    }

    public function show(User $user)
    {
        return ResponseFormatter::success(
            new UserDetailResource($user),
            'success show user data'
        );
    }
    
    public function update(User $user, Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'unique:users,code,' . $user->id],
            'name' => ['required', 'string'],
            'username' => ['required', 'string', 'unique:users,username,'. $user->id],
            'email' => ['required', 'email', 'unique:users,email,'. $user->id],
            'status' => ['required', 'in:active,not_active'],
            'photo' => ['nullable', 'image', 'mimes:png,jpg,jpeg'],
            'department_id' => ['required', 'exists:departments,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'address' => ['nullable', 'string'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $result = DB::transaction(function () use ($request, $user) {
            $input = $request->except([
                'photo',
                'role'
            ]);
            
            // upload file photo if exists
            if(!empty($request->file('photo'))) {
                $input['photo'] = FileHelpers::upload_file(
                    'user/photo-profile', 
                    $request->file('photo'), 
                    'local'
                );

                if($user->photo) {
                    Storage::disk('local')->delete($user->photo);
                }
            }
    
            $user->update($input);
            if(!$user->hasRole($request->role)) {
                $user->syncRoles([]);
                $user->assignRole($request->role);
            }

            return $user;
        });

        return ResponseFormatter::success(
            new UserDetailResource($result),
            'success update user data'
        );
    }

    public function update_photo(Request $request, User $user)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:png,jpg,jpeg'],
        ]);

        try {
            
            // upload file photo
            $path = FileHelpers::upload_file(
                'user/photo-profile', 
                $request->file('photo'), 
                'local'
            );

            // delete photo if exists in user data
            if(isset($user->photo)) {
                Storage::disk('local')->delete($user->photo);
            }
    
            // update colom photo
            $user->update([
                'photo' => $path
            ]);
    
            // return response success
            return ResponseFormatter::success(
                new UserResource($user),
                'success update profile photo'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error([
                'file' => 'An error occurred while uploading the file'
            ], 'upload file failed', 500);
        }
    }
}
