<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlatformRequests\TogglePlatformRequest;
use App\Http\Resources\PlatformResource;
use App\Models\Platform;
use App\Repositories\PlatformRepositoryInterface;

class PlatformController extends Controller
{
    protected $responseHelper;
    protected $platformRepository;

    public function __construct(ResponseHelper $responseHelper, PlatformRepositoryInterface $platformRepository)
    {
        $this->responseHelper = $responseHelper;
        $this->platformRepository = $platformRepository;
    }

    public function index()
    {
        $platforms = $this->platformRepository->getAllPlatforms();

        if ($platforms->isEmpty()) {
            return $this->responseHelper->error('No platforms found', 200);
        }


        return $this->responseHelper->success(
            'Platforms retrieved successfully',
            PlatformResource::collection($platforms)
        );
    }

    public function toggle(TogglePlatformRequest $request)
    {
        $user = $request->user();
        $request->validated();

        if ($user->platforms()->where('platform_id', $request->platform_id)->exists()) {
            $user->platforms()->detach($request->platform_id);
            return $this->responseHelper->success('Platform detached successfully.');
        } else {
            $user->platforms()->attach($request->platform_id);
            return $this->responseHelper->success('Platform attached successfully.');
        }
    }
}
