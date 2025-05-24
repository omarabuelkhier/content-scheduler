<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class PostController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = Post::with('platforms')->where('user_id', $request->user()->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('scheduled_time', $request->date);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'image_url'      => 'nullable|url',
            'scheduled_time' => 'required|date|after:now',
            'status'         => 'required|in:draft,scheduled,published',
            'platforms'      => 'required|array',
            'platforms.*'    => 'exists:platforms,id',
        ]);

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

        return response()->json($post->load('platforms'), 201);
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'title'          => 'sometimes|required|string|max:255',
            'content'        => 'sometimes|required|string',
            'image_url'      => 'nullable|url',
            'scheduled_time' => 'sometimes|required|date|after:now',
            'status'         => 'sometimes|required|in:draft,scheduled,published',
        ]);

        $post->update($request->only('title', 'content', 'image_url', 'scheduled_time', 'status'));

        return response()->json($post);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return response()->json(['message' => 'Post deleted']);
    }
}
