<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);
        $accessToken = $user->createToken('authToken')->accessToken;
        return response()->json(['data' => $user, 'access_token' => $accessToken], 201);
    }
    public function login(LoginRequest $request)
    {
        $loginData = $request->validated();

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response()->json(['data' => auth()->user(), 'access_token' => $accessToken, 'message' => 'Login successfully'], 200);
    }
}
