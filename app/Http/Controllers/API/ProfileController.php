<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $responseHelper;
    protected $userRepository;


    public function __construct(ResponseHelper $responseHelper, UserRepositoryInterface $userRepository)
    {
        $this->responseHelper = $responseHelper;
        $this->userRepository = $userRepository;
    }

    public function show(Request $request)
    {
        return $this->responseHelper->success('User profile retrieved successfully.', new ProfileResource($request->user()));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $request->validated();

        $user = $this->userRepository->updateProfile($request->user()->id, $request->validated());

        return $this->responseHelper->success('User profile updated successfully.', new ProfileResource($user));
    }
}
