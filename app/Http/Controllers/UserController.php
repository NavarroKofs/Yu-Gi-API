<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class UserController extends Controller
{
    //
	     public function store(Request $request)
	    {
	    	if(is_null($request->name)){
	    		     return response()->json([

	               "errors"=> ["code"=> "422",
	               "title"=>  "Unprocessable Entity",
	               "description"=> "Name missing"
	               ]]  , 422);
	    	}elseif (is_null($request->password)) {
	    		     return response()->json([

	               "errors"=> ["code"=> "422",
	               "title"=>  "Unprocessable Entity",
	               "description"=> "Password missing"
	               ]]  , 422);
	    	}elseif (is_null($request->email)) {
	    		     return response()->json([

	               "errors"=> ["code"=> "422",
	               "title"=>  "Unprocessable Entity",
	               "description"=> "Email missing"
	               ]]  , 422);
	    	}else{


	    	 $usuario = DB::table('users')->insert([
	    'email' => $request->email,
	    'name' => $request->name,
	    'password' => $request->password,
	    'remember_token' => Str::random(10),
	    'email_verified_at' => now()
	]);
	    	 $resultado = [

	               "user"=> ["name"=> $request->name,
	               "email"=>  $request->email
	               
	               ]];
	       // $usuario = User::create(request()->all());
	        return response()->json($resultado, 201);
	    	}
	    }
   

}
