<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@gmail.com"),
     *             @OA\Property(property="password", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        // Rate limiting
        $key = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return $this->json(null, "Too many login attempts. Please try again in {$seconds} seconds.", 429);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60); // Block for 1 minute
            return $this->json(null, 'Invalid credentials', 401);
        }

        RateLimiter::clear($key);
        
        // Revoke existing tokens for security
        $user->tokens()->delete();
        
        $token = $user->createToken('authToken', ['*'], now()->addDays(7))->plainTextToken;

        return $this->json([
            'user' => $user->only(['id', 'name', 'email', 'created_at']),
            'token' => $token,
            'expires_at' => now()->addDays(7)->toISOString()
        ], 'Login successful', 200);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="User logout",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return $this->json(null, 'User not authenticated', 401);
            }

            // Delete only current token instead of all tokens
            $user->currentAccessToken()->delete();
            
            return $this->json(null, 'Logout successful', 200);
        } catch (\Exception $e) {
            return $this->json(null, 'Logout failed', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Get current user data",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User data retrieved successfully"
     *     )
     * )
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();
            
            return $this->json([
                'user' => $user->only(['id', 'name', 'email', 'created_at', 'updated_at'])
            ], 'User data retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->json(null, 'Failed to retrieve user data', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="User registration",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", minLength=8)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|min:2',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $token = $user->createToken('authToken', ['*'], now()->addDays(7))->plainTextToken;

            return $this->json([
                'user' => $user->only(['id', 'name', 'email', 'created_at']),
                'token' => $token,
                'expires_at' => now()->addDays(7)->toISOString()
            ], 'Registration successful', 201);
            
        } catch (ValidationException $e) {
            return $this->json(null, 'Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->json(null, 'Registration failed', 500);
        }
    }
}
