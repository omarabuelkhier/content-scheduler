<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function getUserPosts(int $userId, array $filters = [])
    {
        $query = $this->model->where('user_id', $userId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date'])) {
            $query->whereDate('scheduled_time', $filters['date']);
        }

        return $query->with('platforms')->latest()->get();
    }
}
