<?php

namespace App\Http\Controllers\Auth;
use Auth;
use App\Http\Controllers\Controller;
//use App\User;


class LoginController extends Controller
{
  public function login(){
    $credentials = $this->validate(request(),
        [
            'email'=>'email|required|string',
            'password' => 'required|string'
        ]);
  
    if(Auth::attempt($credentials)){
        return response()->json($credentials, 200);
    }else{

    return response()->json([
        "errors"=> [
            "code"=>"422",

            "description"=>"Unprocessable Entity",
            
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

    ]], 422);
    }else{

    Auth::logout();
    return response()->json( "Session closed",200);
    }
  }
 
}
