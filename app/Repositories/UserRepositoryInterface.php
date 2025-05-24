<?php

namespace App\Repositories;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function updateProfile(int $userId, array $data);
}
