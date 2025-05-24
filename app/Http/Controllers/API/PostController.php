<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequests\DestroyPostRequest;
use App\Http\Requests\PostRequests\StorePostRequest;
use App\Http\Requests\PostRequests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Repositories\PostRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    protected $responseHelper;
    protected $postRepository;

    public function __construct(ResponseHelper $responseHelper, PostRepositoryInterface $postRepository)
    {
        $this->responseHelper = $responseHelper;
        $this->postRepository = $postRepository;
    }

    /**
     * Display a listing of the posts for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'date']);
        $posts = $this->postRepository->getUserPosts($request->user()->id, $filters);
        if ($posts->isEmpty()) {
            return $this->responseHelper->error('No posts found for the user.', 200);
        }
        return $this->responseHelper->success('Posts retrieved successfully.', PostResource::collection($posts));
    }
    /*
        * Display a listing of all posts.
        *
        * @param Request $request
        * @return \Illuminate\Http\JsonResponse
        */
    public function allPosts(Request $request)
    {
        // $this->authorize('viewAny', Post::class);
        $filters = $request->only(['status', 'date']);
        $posts = $this->postRepository->getAllPosts($filters);
        if ($posts->isEmpty()) {
            return $this->responseHelper->error('No posts found.', 200);
        }
        return $this->responseHelper->success('Posts retrieved successfully.', PostResource::collection($posts));
    }

    /**
     * Display the specified post.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePostRequest $request)
    {
        $this->authorize('create', Post::class);
        // Validate the request data
        $data = $request->validated();

        $post = $this->postRepository->create($data);
        // Return a success response with the created post
        if (!$post) {
            return $this->responseHelper->error('Failed to create post.', 200,);
        }
        return $this->responseHelper->success('Post created successfully.', new PostResource($post), 201);
    }
    /**
     * Display the specified post.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $this->authorize('view', Post::class);
        $post = $this->postRepository->getPostById($id);
        if (!$post) {
            return $this->responseHelper->error('Post not found.', 404);
        }
        return $this->responseHelper->success('Post retrieved successfully.', new PostResource($post));
    }
    /*
        * Update the specified post.
        *
        * @param UpdatePostRequest $request
        * @param int $id
        * @return \Illuminate\Http\JsonResponse
        */
    public function update(UpdatePostRequest $request, $id)
    {
        $this->authorize('update', Post::class);
        $data = $request->validated();
        $post = $this->postRepository->update($id, $data);
        if (!$post) {
            return $this->responseHelper->error('Failed to update post.', 200);
        }
        // Return a success response with the updated post
        return $this->responseHelper->success('Post updated successfully.', new PostResource($post));
    }
    /**
     * Remove the specified post from storage.
     *
     * @param DestroyPostRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DestroyPostRequest $request, $id)
    {
        $this->authorize('delete', Post::class);

        $this->postRepository->delete($id);
        return $this->responseHelper->success('Post deleted successfully.');
    }
}
