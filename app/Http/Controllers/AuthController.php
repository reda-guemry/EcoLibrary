<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    public function register(RegisterRequest $request) 
    {

        $user = User::create([
            'name' => $request->name , 
            'email' => $request->email , 
            'password' => Hash::make($request->password) , 
            'role' => $request->role ?? 'user' ,
        ]); 

        $token = $user->createToken('auth_token')->plainTextToken ; 

        return response()->json([
            'message' => 'User registered successfully' ,
            'token' => $token ,
            'user'
        ]) ;


    }


}
