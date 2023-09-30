<?php

namespace App\Http\Controllers\API\User\Auth;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        // validasi form input
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required']
        ]);

        try {
            // check credential login
            if (!Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 422);
            }
    
            // if Hash or password not valid
            $user = User::where('username', $request->username)->first();

            if(empty($user)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 422);
            }

            if (!Hash::check($request->password, $user->password)) {
                throw new \Exception('Invalid Credentials');
            }

            if($user->status == 'not_active') {
                return ResponseFormatter::error([
                    'message' => 'Your account is not active yet',
                ], 'Authentication Failed', 422);
            }

            // if success then login
            $tokenResult = $user->createToken('authToken')->accessToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => new UserResource($user)
            ], 'Authenticated');
        } catch (Exception $e) {
            return ResponseFormatter::error([
                'message' => 'server error'
            ], 'Authentication Failed', 500);
        }

    }
}
