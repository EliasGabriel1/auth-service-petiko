<?php

/**
 * @OA\Info(
 *     title="Auth Service API",
 *     version="1.0.0",
 *     description="Authentication service API"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $service)
    {
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User registered successfully")
     * )
     */
    public function register(Request $request)
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $result = $this->service->register($payload);

        return response()->json([
            'user' => $result['user']->only(['id', 'name', 'email', 'is_admin', 'created_at']),
            'token' => $result['token'],
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@test.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login successful"),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $payload = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $result = $this->service->login($payload['email'], $payload['password']);

        if (! $result) {
            return response()->json([
                'message' => 'Credenciais inválidas',
            ], 401);
        }

        return response()->json([
            'user' => $result['user']->only(['id', 'name', 'email', 'is_admin', 'created_at']),
            'token' => $result['token'],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Get authenticated user",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Authenticated user returned"),
     *     @OA\Response(response=401, description="Token missing or invalid")
     * )
     */
    public function me(Request $request)
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json([
                'message' => 'Token não fornecido',
            ], 401);
        }

        $user = $this->service->me($token);

        if (! $user) {
            return response()->json([
                'message' => 'Token inválido',
            ], 401);
        }

        return response()->json($user->only(['id', 'name', 'email', 'is_admin', 'created_at']));
    }
}
