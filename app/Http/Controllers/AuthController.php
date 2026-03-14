<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    /**
     *@OA\Post(
     *path="/api/auth/login",
     *tags={"Authentication"},
     *summary="Login a user",
     *description="Authenticate a user and return an access token.",
     *@OA\RequestBody(
     *  required=true,
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="admin@ecolibrary.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123") , 
     * )
     * ),
     * @OA\Response(
     * response=200 ,
     * description="User logged in successfully",
     * @OA\JsonContent(
     * @OA\Property(property="user" , type="object" ) , 
     * @OA\Property(property="token" , type="string" , example="4|2mMJFE34stq44vErSOYWlcT09UqlvJiL0wzRHx4853505207") 
     * )
     * ),
     * @OA\Response(
     * response=401 , 
     * description="Invalid credentials",
     * )
     * )
     */

    public function login(LoginRequest $request)
    {

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }



    /**
     * @OA\Post(
     * path="/api/register",
     * tags={"Authentication"},
     * summary="Register a new user",
     * description="Create a new user account and return an access token.",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name","email","password"},
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="john@ecolibrary.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="User registered successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="User registered successfully"),
     * @OA\Property(property="token", type="string", example="1|laravel_sanctum_token_here"),
     * @OA\Property(property="user", type="object")
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Bad request"
     * )
     * )
     */

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'User logged out successfully',
        ]);
    }

}
