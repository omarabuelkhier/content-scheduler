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
        $user = $request->user();

        if ($request->wantsJson()) {
            return $this->responseHelper->success('User profile retrieved successfully.', new ProfileResource($user));
        }

        return view('profile.show', ['user' => $user]);
    }
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $request->validated();

        $user = $this->userRepository->updateProfile($request->user()->id, $request->validated());

        if ($request->wantsJson()) {
            return $this->responseHelper->success('User profile updated successfully.', new ProfileResource($user));
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }
}
