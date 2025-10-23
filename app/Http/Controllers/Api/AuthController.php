<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
  public function __construct(private AuthService $authService) {}

  public function register(RegisterRequest $request): JsonResponse
  {
    $result = $this->authService->register($request->validated());

    return $this->successResponse([
      'user' => new UserResource($result['user']),
      'access_token' => $result['token'],
      'token_type' => 'Bearer'
    ], 'User registered successfully', 201);
  }

  public function login(LoginRequest $request): JsonResponse
  {
    $result = $this->authService->login($request->validated());

    return $this->successResponse([
      'user' => new UserResource($result['user']),
      'access_token' => $result['token'],
      'token_type' => 'Bearer'
    ], 'Login successful');
  }

  public function profile(Request $request): JsonResponse
  {
    return $this->successResponse([
      'user' => new UserResource($request->user())
    ], 'Profile retrieved successfully');
  }

  public function logout(Request $request): JsonResponse
  {
    $this->authService->logout();
    return $this->successResponse(null, 'Logged out successfully');
  }

  public function logoutAll(Request $request): JsonResponse
  {
    $this->authService->logoutAll();

    return $this->successResponse(null, 'Logged out from all devices successfully');
  }
}
