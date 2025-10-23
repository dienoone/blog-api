<?php

namespace App\Services;

use App\Models\User;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ConflictException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthService
{
  public function getAuthUser(): User
  {
    return Auth::user();
  }

  public function register(array $data): array
  {
    return DB::transaction(function () use ($data) {
      // Check if email already exists
      if (User::where('email', $data['email'])->exists()) {
        throw new ConflictException('Email already registered');
      }

      // Create user
      $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
      ]);

      // Generate token
      $token = $user->createToken('auth-token')->plainTextToken;

      return [
        'user' => $user->load('role'),
        'token' => $token,
      ];
    });
  }

  public function login(array $data): array
  {
    $user = User::where('email', $data['email'])->first();

    if (!$user || !Hash::check($data['password'], $user->password)) {
      throw new UnauthorizedException('Invalid credentials');
    }

    // Delete old tokens (optional - for single device login)
    // $user->tokens()->delete();

    $token = $user->createToken('auth-token')->plainTextToken;

    return [
      'user' => $user,
      'token' => $token,
    ];
  }

  public function logout(): void
  {
    $user = Auth::user();

    // Delete current token
    $token = $user->tokens()->where('id', $user->currentAccessToken()->id)->first();

    if ($token) {
      $token->delete();
    }
  }

  public function logoutAll(): void
  {
    // Delete all tokens
    Auth::user()->tokens()->delete();
  }
}
