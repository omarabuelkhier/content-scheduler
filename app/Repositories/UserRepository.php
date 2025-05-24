<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function updateProfile(int $userId, array $data)
    {
        $user = $this->find($userId);
        $user->update($data);
        return $user;
    }
}
