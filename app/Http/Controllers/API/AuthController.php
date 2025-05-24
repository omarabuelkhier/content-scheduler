<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $responseHelper;

    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

    public function register(RegisterRequest $request)
    {
        try {
            // Check if the user already exists
            if (User::where('email', $request->email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User with this email already exists.',
                ], 409);
            }

            // Create a new user
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
            ]);
            // Generate an authentication token
            $token = $user->createToken('auth_token')->plainTextToken;
            if (! $token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create authentication token.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully.',
                'data'    => [
                    'token' => $token,
                    'user'  => new UserResource($user),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        // Validate the request
        $request->validated();

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
                'user'  => new UserResource($user),
            ]
        );
    }

    public function logout(Request $request)
    {

        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }
        // check if the user is logged OUT
        if ($user->tokens()->count() <= 0) {
            Log::info('User tokens:', $user->tokens()->toArray());

            return $this->responseHelper->error(
                'User is already logged out.',
                403
            );
        }

        $user->tokens()->delete();
        return $this->responseHelper->success('Logged out successfully.');
    }
}
