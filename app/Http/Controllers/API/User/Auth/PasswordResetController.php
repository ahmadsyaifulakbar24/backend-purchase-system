<?php

namespace App\Http\Controllers\API\User\Auth;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function password_email(Request $request) 
    {
        $request->validate([
            'email' => [
                'required', 
                'email', 
                Rule::exists('users', 'email')
            ]
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );
        if ($status === Password::RESET_LINK_SENT) {
            return ResponseFormatter::success(null, ['status' => __($status)]);
        } else {
            return ResponseFormatter::error(null, ['email' => __($status)]);
        }
        
    }

    public function password_update (Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => [
                'required', 
                'email', 
                Rule::exists('users', 'email')
            ],
            'password' => ['required', 'confirmed', 'min:8'],
            'password_confirmation' => ['required', 'min:8']
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
     
                $user->save();
     
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            
            return ResponseFormatter::success(null, ['status' => __($status)]);
        } else {
            return ResponseFormatter::errorValidation(['email' => [__($status)]], 'reset password failed');
        } 
    }

    public function without_confirmation(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'password' => ['required', 'confirmed', 'min:8'],
            'password_confirmation' => ['required', 'min:8'],
        ]);

        $user = User::find($request->user_id);
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        return ResponseFormatter::success(new UserResource($user), 'success reset password data');
    }
    
    public function with_old_password (Request $request)
    {
        $request->validate([
            'old_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
            'password_confirmation' => ['required', 'min:8'],
        ]);

        $user = User::find(Auth::user()->id);
        if(!Hash::check($request->old_password, $user->password)) {
            return ResponseFormatter::errorValidation([
                'old_password' => 'old password is invalid'
            ], 'reset password failed', 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);
        return ResponseFormatter::success(new UserResource($user), 'success reset password data');
    }
}
