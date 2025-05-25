<?php

namespace App\Repositories;

interface PostRepositoryInterface extends BaseRepositoryInterface
{
    public function getUserPosts(int $userId, array $filters = [], int $perPage );
    public function getAllPosts(array $filters = [], int $perPage );
    public function getPostById(int $postId);
    public function createPost(array $data);
    public function updatePost(int $postId, array $data);
    public function deletePost(int $postId);
}
