<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function showResetForm(Request $request, $token = null)
    {
        $exist = DB::table('password_reset')
            ->where('token', $token)->first();
        if($exist){
            return view('auth.reset_password', [
                'token' => $token
            ]);
        }else{
            return redirect('/');
        }
    }

    public function reset(Request $request)
    {
        $token = $request->pass_token;
        $result = DB::table('password_reset')
            ->where('token', $token)->first();
        if($result){
            $email = $result->email;
            $user = User::where('email',$email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            $result = DB::table('password_reset')
                ->where('token', $token)->delete();
            return response()->json(['result'=>true,'success'=>true]);
        }else{
            return response()->json(['result'=>false]);
        }
    }
}
