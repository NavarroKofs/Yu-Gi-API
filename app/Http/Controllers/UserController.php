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
	    	 $credentials = $this->validate($request,
        [
            'email'=>'email|required|string',
            'password' => 'required|string',
            'name'=> 'required|string'
        ]);
	    	if($credentials){

	       	$usuario = User::create(request()->all());
	    	 $resultado = [

	               "user"=> ["name"=> $request->name,
	               "email"=>  $request->email
	               
	               ]];
	        return response()->json($resultado, 201);
	    	}
	    }
   

}
