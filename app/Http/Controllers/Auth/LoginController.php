<?php

namespace App\Http\Controllers\Auth;
use Auth;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
  public function login(){
    $credentials = $this->validate(request(),
        [
            'email'=>'email|required|string',
            'password' => 'required|string'
        ]);
    //return $credentials;
    if(auth::attempt($credentials)){
        return response()->json($credentials, 201);
    }else{

    return response()->json([
        "errors"=> ["code"=>"Error-1",
        "title"=>"El usuario no se encuentra registrado"
    ]], 422);
    }
  }
  public function logout(){
    $this->middleware('auth');
    Auth::logout();
    return response()->json( "Haz salido de la sesion",200);
  }
 
}
