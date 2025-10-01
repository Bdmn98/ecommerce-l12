<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private AuthService $service)
    {
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->service->register($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successfulResponse(
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            __('Registered'),
            Response::HTTP_CREATED,
            ['token' => $token]
        );
    }

    public function login(LoginRequest $request)
    {
        $res = $this->service->login($request->email, $request->password);
        return $this->successfulResponse($res, __('Logged in'));
    }

    public function logout(Request $request)
    {
        $this->service->logout($request->user());
        return $this->successfulResponse(null, __('Logged out'));
    }

    public function me(Request $request)
    {
        return $this->successfulResponse($request->user(), __('OK'));
    }
}
