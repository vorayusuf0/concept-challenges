<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Facades\ {
    App\Services\AuthService
};
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = AuthService::register($validatedData);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Registration failed'], 500);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json(['success' => true, 'message' => 'User registered successfully', 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        return response()->json(['success' => true, 'message' => 'Login successful', 'token' => $token], 200);
    }

    public function getCurrentUser(Request $request)
    {
        $user = auth()->user();
        return response()->json(['success' => true, 'data' => $user], 200);
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        auth()->logout();
        return response()->json(['success' => true, 'message' => 'Logout successful'], 200);
    }
}
