<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequests\DestroyPostRequest;
use App\Http\Requests\PostRequests\StorePostRequest;
use App\Http\Requests\PostRequests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Repositories\PlatformRepository;
use App\Repositories\PlatformRepositoryInterface;
use App\Repositories\PostRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    protected $responseHelper;
    protected $postRepository;

    protected $platformRepository;
    public function __construct(ResponseHelper $responseHelper, PostRepositoryInterface $postRepository,PlatformRepositoryInterface $platformRepository)
    {
        $this->responseHelper = $responseHelper;
        $this->postRepository = $postRepository;
        $this->platformRepository = $platformRepository;
    }

    /**
     * Display a listing of the posts for the authenticated user.

     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'date']);
        $perPage = $request->get('per_page', 10); // Default to 10 items per page

        $posts = $this->postRepository->getUserPosts($request->user()->id, $filters, $perPage);

        // Check if the request is an API request
        if ($request->wantsJson()) {
            return $this->responseHelper->paginatedSuccess(
                'Posts retrieved successfully.',
                PostResource::collection($posts->items()),
                [
                    'current_page' => $posts->currentPage(),
                    'last_page'    => $posts->lastPage(),
                    'per_page'     => $posts->perPage(),
                    'total'        => $posts->total(),
                ]
            );
        }

        // For web requests, render the dashboard view
        return view('posts.mine', ['posts' => $posts]);
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
        if ($request->wantsJson()) {

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
        // For web requests, render the dashboard view
        return view('posts.index', ['posts' => $posts]);
    }

    /**
     * Display the specified post.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
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
        if ($request->wantsJson()) {
            return $this->responseHelper->success('Post created successfully.', new PostResource($post), 201);
        }
        // For web requests, redirect to the posts index page
        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified post.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $post = $this->postRepository->getPostById($id);
        if (!$post) {
            return $this->responseHelper->error('Post not found.', 404);
        }
        if ($request->wantsJson()) {
            return $this->responseHelper->success('Post retrieved successfully.', new PostResource($post));
        }

        return view('posts.show', ['post' => $post]);
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
        if ($request->wantsJson()) {

            return $this->responseHelper->success('Post updated successfully.', new PostResource($updatedPost));
        }
        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');

    }

public function edit($id)
{
    // Retrieve the post by ID
    $post = $this->postRepository->getPostById($id);

    if (!$post) {
        abort(404, 'Post not found.');
    }

    // Retrieve all platforms for the dropdown
    $platforms = $this->platformRepository->getAllPlatforms();

    // Return the edit view with the post and platforms
    return view('posts.edit', [
        'post' => $post,
        'platforms' => $platforms,
    ]);
}
    /**
     * Remove the specified post from storage.
     *
     * @param DestroyPostRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(DestroyPostRequest $request, $id)
    {
        $post = $this->postRepository->getPostById($id);

        if (!$post) {
            return $this->responseHelper->error('Post not found.', 404);
        }

        $this->authorize('delete', $post);

        $this->postRepository->deletePost($id);
        if ($request->wantsJson()) {

            return $this->responseHelper->success('Post deleted successfully.');
        }
        // For web requests, redirect to the posts index page
        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
}
