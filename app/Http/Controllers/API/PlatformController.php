<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlatformRequests\TogglePlatformRequest;
use App\Http\Resources\PlatformResource;
use App\Models\Platform;
use App\Repositories\PlatformRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function getMyAttachedPlatforms(Request $request)
    {
        $user = $request->user();

        // Fetch unique platforms from the user's posts
        $userPlatforms = $user->posts()
            ->with('platforms') // Load platforms for the posts
            ->get()
            ->pluck('platforms') // Extract platforms from each post
            ->flatten() // Flatten the nested collections
            ->unique('id') // Remove duplicate platforms by ID
            ->values(); // Reindex the collection

        if ($userPlatforms->isEmpty()) {
            return $this->responseHelper->error('No platforms found for the user\'s posts.', 404);
        }

        return $this->responseHelper->success(
            'Unique platforms retrieved successfully.',
            PlatformResource::collection($userPlatforms)
        );
    }

    /**
     * Toggle the user's platform subscription.
     *
     * @param TogglePlatformRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(TogglePlatformRequest $request)
    {
        $user = $request->user();
        $request->validated();

        // Fetch all posts belonging to the user
        $userPosts = $user->posts()->pluck('id');

        // Check if the platform is attached to any of the user's posts
        $platformAttached = DB::table('post_platform')
            ->whereIn('post_id', $userPosts)
            ->where('platform_id', $request->platform_id)
            ->exists();

        if (!$platformAttached) {
            return $this->responseHelper->error('This platform is not attached to any of the user\'s posts.', 404);
        }

        // Toggle the platform for all posts belonging to the user
        if ($platformAttached) {
            DB::table('post_platform')
                ->whereIn('post_id', $userPosts)
                ->where('platform_id', $request->platform_id)
                ->delete();

            return $this->responseHelper->success('Platform detached successfully.');
        } else {
            foreach ($userPosts as $postId) {
                DB::table('post_platform')->insert([
                    'post_id'       => $postId,
                    'platform_id'   => $request->platform_id,
                    'platform_status' => 'pending',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            return $this->responseHelper->success('Platform attached successfully.');
        }
    }
}
