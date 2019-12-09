<?php

namespace App\Http\Controllers\Auth;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use App\User;


class LoginController extends Controller
{
  public function login(Request $request){
    $credentials = $this->validate($request,
        [
            'email'=>'email|required|string',
            'password' => 'required|string'
        ]);
  
    
  
    if(Auth::attempt($credentials)){
        return response()->json($credentials, 200);
    }else{

    return response()->json([
        "errors"=> [
            "code"=>"401",

            "description"=>"Unauthorized",
            
    ]], 401);
    }
  }

  
  public function logout(){
    $sesionCerrada = $this->middleware('auth');
    if(!$sesionCerrada){

        return response()->json([
            
        "errors"=> [
            "code"=>"Error-1",

            "title"=>"Unprocesable Entity",

    ]], 401);
    }else{

    Auth::logout();
    return response()->json( "Session closed",200);
    }
  }
 
}
