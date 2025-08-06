<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !password_verify($request->password, $user->password)) {
            return $this->json(null, 'Invalid credentials', 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return $this->json([
            'user' => $user,
            'token' => $token,
        ], 'Login successful', 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->json(null, 'Logout successful', 200);
    }

    public function me(Request $request)
    {
        return $this->json($request->user(), 'User data', 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return $this->json([
            'user' => $user,
            'token' => $token,
        ], 'Registration successful', 201);
    }
}
