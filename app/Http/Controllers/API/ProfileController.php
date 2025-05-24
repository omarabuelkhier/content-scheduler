<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $responseHelper;

    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

    public function show(Request $request)
    {
        return $this->responseHelper->success('User profile retrieved successfully.', new ProfileResource($request->user()));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $request->validated();

        $user->update($request->only('name', 'email'));

        return $this->responseHelper->success('User profile updated successfully.', new ProfileResource($user));
    }
}
