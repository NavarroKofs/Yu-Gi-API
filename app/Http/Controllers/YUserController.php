<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class YUserController extends Controller
{
    //
     public function store(Request $request)
    {
    	
        $usuario = User::create(request()->all());
        return response()->json($usuario, 201);
    }
    public function edit_Password(Request $request)
    {
        //
    }

}
