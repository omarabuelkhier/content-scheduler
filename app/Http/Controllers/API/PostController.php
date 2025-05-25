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
        $perPage = $request->get('per_page', 10); // Default to 10 items per page

        $posts = $this->postRepository->getUserPosts($request->user()->id, $filters, $perPage);

        if ($posts->isEmpty()) {
            return $this->responseHelper->error('No posts found for the user.', 404);
        }

        return $this->responseHelper->paginatedSuccess(
            'Posts retrieved successfully.',
            [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'per_page'     => $posts->perPage(),
                'total'        => $posts->total(),
            ],
            PostResource::collection($posts->items()),

        );
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
        $perPage = $request->get('per_page', 10); // Default to 10 items per page

        $posts = $this->postRepository->getAllPosts($filters, $perPage);

        if ($posts->isEmpty()) {
            return $this->responseHelper->error('No posts found.', 404);
        }

        return $this->responseHelper->paginatedSuccess(
            'Posts retrieved successfully.',
            [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'per_page'     => $posts->perPage(),
                'total'        => $posts->total(),
            ],
            PostResource::collection($posts->items())
        );
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

        // Add the authenticated user's ID to the data
        $data['user_id'] = $request->user()->id;

        $post = $this->postRepository->createPost($data);

        // Attach platforms to the post
        if (isset($data['platforms']) && is_array($data['platforms'])) {
            $post->platforms()->sync($data['platforms']);
        }

        // Return a success response with the created post
        if (!$post) {
            return $this->responseHelper->error('Failed to create post.', 500);
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
        $post = $this->postRepository->getPostById($id);
        if (!$post) {
            return $this->responseHelper->error('Post not found.', 404);
        }
        $this->authorize('update', $post);
        $data = $request->validated();
        $updatedPost = $this->postRepository->updatePost($id, $data);
        if (!$updatedPost) {
            return $this->responseHelper->error('Failed to update post.', 500);
        }
        // Return a success response with the updated post
        return $this->responseHelper->success('Post updated successfully.', new PostResource($updatedPost));
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
        $post = $this->postRepository->getPostById($id);

        if (!$post) {
            return $this->responseHelper->error('Post not found.', 404);
        }

        $this->authorize('delete', $post);

        $this->postRepository->deletePost($id);
        return $this->responseHelper->success('Post deleted successfully.');
    }
}
