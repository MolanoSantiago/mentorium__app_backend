<?php

namespace App\Http\Controllers;

use App\Constants\TokenAbilityEnum;
use App\Http\Requests\AuthRequest;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function login(AuthRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'phone', 'password');

        $loginField = !empty($credentials['email']) ? 'email' : 'phone';
        $loginValue = $credentials[$loginField];

        if (auth()->attempt([$loginField => $loginValue, 'password' => $credentials['password']])) {
            $user = auth()->user();

            $accessToken = $user->createToken(
                'access_token',
                [TokenAbilityEnum::ACCESS_API->value],
                Carbon::now()->addMinutes((int)config('sanctum.access_token_expiration'))
            );

            $refreshToken = $user->createToken(
                'refresh_token',
                [TokenAbilityEnum::ISSUE_ACCESS_TOKEN->value],
                Carbon::now()->addMinutes((int)config('sanctum.refresh_token_expiration'))
            );

            return AuthResource::make($user)->additional([
                'access_token' => $accessToken->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken,
                'token_type' => 'Bearer'
            ])->response()->setStatusCode(200);
        }

        return AuthResource::make([])->additional([
            'message' => 'Unauthorized'
        ])->response()->setStatusCode(401);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return AuthResource::make([])->additional([
            'message' => 'Logged out'
        ])->response()->setStatusCode(200);
    }

    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->tokens()->where('name', 'refresh_token')->delete();

        $accessToken = $user->createToken(
            'access_token',
            [TokenAbilityEnum::ACCESS_API->value],
            Carbon::now()->addMinutes((int)config('sanctum.access_token_expiration'))
        );

        return AuthResource::make($user)->additional([
            'access_token' => $accessToken->plainTextToken,
            'token_type' => 'Bearer'
        ])->response()->setStatusCode(200);
    }

    public function signup(AuthRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create($data);

        $accessToken = $user->createToken(
            'access_token',
            [TokenAbilityEnum::ACCESS_API->value],
            Carbon::now()->addMinutes((int)config('sanctum.access_token_expiration'))
        );

        $refreshToken = $user->createToken(
            'refresh_token',
            [TokenAbilityEnum::ISSUE_ACCESS_TOKEN->value],
            Carbon::now()->addMinutes((int)config('sanctum.refresh_token_expiration'))
        );

        return AuthResource::make($user)->additional([
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'token_type' => 'Bearer'
        ])->response()->setStatusCode(201);
    }
}
