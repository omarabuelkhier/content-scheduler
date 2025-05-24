<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequests\DestroyPostRequest;
use App\Http\Requests\PostRequests\StorePostRequest;
use App\Http\Requests\PostRequests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    protected $responseHelper;

    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

    public function index(Request $request)
    {
        $query = Post::with('platforms')->where('user_id', $request->user()->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('scheduled_time', $request->date);
        }

        return $this->responseHelper->success('Posts retrieved successfully.', PostResource::collection($query->latest()->get()));
    }

    public function store(StorePostRequest $request)
    {
        // Enforce rate limit: Max 10 scheduled posts per day
        $scheduledPostsCount = Post::where('user_id', $request->user()->id)
            ->where('status', 'scheduled')
            ->whereDate('scheduled_time', now()->toDateString())
            ->count();

        if ($scheduledPostsCount >= 10) {
            return $this->responseHelper->error('You can only schedule up to 10 posts per day.', 429);
        }

        $request->validated();

        $post = Post::create([
            'title'          => $request->title,
            'content'        => $request->content,
            'image_url'      => $request->image_url,
            'scheduled_time' => $request->scheduled_time,
            'status'         => $request->status,
            'user_id'        => $request->user()->id,
        ]);

        foreach ($request->platforms as $platform_id) {
            $post->platforms()->attach($platform_id, ['platform_status' => 'pending']);
        }

        return $this->responseHelper->success('Post created successfully.', PostResource::collection($post->load('platforms')), 201);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validated();

        $post->update($request->only('title', 'content', 'image_url', 'scheduled_time', 'status'));

        return $this->responseHelper->success('Post updated successfully.', PostResource::collection($post));
    }

    public function destroy(DestroyPostRequest $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return $this->responseHelper->success('Post deleted successfully.');
    }
}
