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
        return response()->json($credentials, 200);
    }else{

    return response()->json([
        "errors"=> [
            "code"=>"Error-1",

            "title"=>"Unprocesable Entity",
            "description" => "El usuario no tiene una cuenta activa"
    ]], 422);
    }
  }
  public function logout(){
    $sesionCerrada = $this->middleware('auth');
    if(!$sesionCerrada){

        return response()->json([
            
        "errors"=> [
            "code"=>"Error-1",

            "title"=>"Unprocesable Entity",
            "description" => "Ha ocurrido un error con el cierre de sesion"
    ]], 422);
    }else{

    Auth::logout();
    return response()->json( "Haz salido de la sesion",200);
    }
  }
 
}
