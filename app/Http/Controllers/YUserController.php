<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
class YUserController extends Controller
{
    //
	     public function store(Request $request)
	    {
	    	if(is_null($request->name)){
	    		     return response()->json([

	               "errors"=> ["code"=> "ERROR-1",
	               "title"=>  "Unprocessable Entity",
	               "description"=> "Es necesario ingresar un nombre"
	               ]]  , 422);
	    	}elseif (is_null($request->password)) {
	    		     return response()->json([

	               "errors"=> ["code"=> "ERROR-1",
	               "title"=>  "Unprocessable Entity",
	               "description"=> "Es necesario ingresar una contrasena"
	               ]]  , 422);
	    	}elseif (is_null($request->email)) {
	    		     return response()->json([

	               "errors"=> ["code"=> "ERROR-1",
	               "title"=>  "Unprocessable Entity",
	               "description"=> "Es necesario ingresar un email"
	               ]]  , 422);
	    	}else{


	    	 $usuario = DB::table('users')->insert([
	    'email' => $request->email,
	    'name' => $request->name,
	    'password' => $request->password
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
