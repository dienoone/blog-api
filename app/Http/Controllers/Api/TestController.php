<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exceptions\ValidationException;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    public function testSuccess(): JsonResponse
    {
        return $this->successResponse(['message' => 'API is working!'], 'Success test');
    }

    public function testBadRequest(): JsonResponse
    {
        throw new BadRequestException('This is a bad request test');
    }

    public function testNotFound(): JsonResponse
    {
        throw new NotFoundException('Resource not found test');
    }

    public function testValidation(): JsonResponse
    {
        throw new ValidationException(
            'Validation failed test',
            null,
            [
                'email' => ['Email is required', 'Email must be valid'],
                'password' => ['Password is required']
            ]
        );
    }
}
