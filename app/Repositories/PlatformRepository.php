<?php

namespace App\Repositories;

use App\Models\Platform;

class PlatformRepository extends BaseRepository implements PlatformRepositoryInterface
{
    public function __construct(Platform $model)
    {
        parent::__construct($model);
    }

    public function getAllPlatforms()
    {
        return $this->model->all();
    }
}
