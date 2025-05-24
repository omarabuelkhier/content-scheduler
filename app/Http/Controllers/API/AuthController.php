<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $responseHelper;

    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return $this->responseHelper->success(
            'User registered successfully.',
            [
                'token' => $user->createToken('auth_token')->plainTextToken,
                'user'  => $user,
            ],
            201
        );
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->responseHelper->error(
                'The provided credentials are incorrect.',
                401
            );
        }

        return $this->responseHelper->success(
            'Login successful.',
            [
                'token' => $user->createToken('auth_token')->plainTextToken,
                'user'  => $user,
            ]
        );
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->responseHelper->success('Logged out successfully.');
    }
}
