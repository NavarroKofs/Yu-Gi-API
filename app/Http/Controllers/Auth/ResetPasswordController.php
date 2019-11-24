<?php

namespace App\Http\Controllers\Auth;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\User;
use Illuminate\Http\Request;
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
    protected $redirectTo = '/home';

    public function resetPassword( Request $email){

        //Validacion de input
        $validacion = $this->validate($email, [
            'password' => 'required|string'
        ]
);
      
       $id = $email->id;
      
       $actualizacion = request()->except(['_token', '_method']);
        User::where('id', "=", $id)->update($actualizacion);
                $producto = User::findOrFail($id);
         return response()->json($producto,201);
    }
}
