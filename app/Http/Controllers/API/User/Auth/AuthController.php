<?php

namespace App\Http\Controllers\API\User\Auth;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __invoke()
    {
        $user = User::find(Auth::user()->id);
        return ResponseFormatter::success(
            new UserResource($user), 
            'success get auth user data'
        );
    }
}
