<?php

namespace App\Repositories;

interface PostRepositoryInterface extends BaseRepositoryInterface
{
    public function getUserPosts(int $userId, array $filters = []);
}
