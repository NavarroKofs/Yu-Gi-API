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
  

  /*
    if(Auth::attempt($credentials)){
        return response()->json($credentials, 200);
    }else{

    return response()->json([
        "errors"=> [
            "code"=>"401",

            "description"=>"Unauthorized",
            
    ]], 401);
    }*/
  }

  
  public function logout(Request $request)
  {
      $user = Auth::guard('api')->user();
  
      if ($user) {
          $user->api_token = null;
          $user->save();
      }
  
      return response()->json(['data' => 'User logged out.'], 200);
  }
 
}
