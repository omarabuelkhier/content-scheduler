<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function getUserPosts(int $userId, array $filters = [], int $perPage = 10)
    {
        $query = $this->model->where('user_id', $userId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date'])) {
            $query->whereDate('scheduled_time', $filters['date']);
        }

        return $query->with('platforms')->latest()->paginate($perPage);
    }
    public function getAllPosts(array $filters = [],int $perPage = 10)
    {
        $query = $this->model;

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date'])) {
            $query->whereDate('scheduled_time', $filters['date']);
        }

        return $query->with('platforms')->latest()->paginate($perPage);
    }
    public function getPostById(int $postId)
    {
        return $this->model->with('platforms')->findOrFail($postId);
    }
    public function createPost(array $data)
    {
        return $this->model->create($data);
    }
    public function updatePost(int $postId, array $data)
    {
        $post = $this->getPostById($postId);
        $post->update($data);
        return $post;
    }
    public function deletePost(int $postId)
    {
        $post = $this->getPostById($postId);
        $post->delete();
        return $post;
    }
}
