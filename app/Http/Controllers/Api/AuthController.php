<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    use ResponseTrait;


    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = User::create($data);

        $token = JWTAuth::fromUser($user);
        $payload = (new UserResource($user))->resolve();


        return $this->response(
            'success',
            'User registered successfully',
            $payload,
            ['token' => $token]
        );
    }


    /**
     * Login a user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (! $token = JWTAuth::attempt($credentials)) {
            return $this->response('fail', 'Invalid credentials');
        }

        $user    = auth()->user();
        $payload = (new UserResource($user))->resolve();

        return $this->response(
            'success',
            'Login successful',
            $payload,
            ['token' => $token]
        );
    }



    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user();
        $payload = (new UserResource($user))->resolve();

        return $this->response('success', 'User data', $payload);
    }


    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());
        return $this->response('success', 'Token refreshed successfully', [], ['token' => $token]);
    }

    /**
     * Logout a user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->response('success', 'Logged out successfully');
    }
}
