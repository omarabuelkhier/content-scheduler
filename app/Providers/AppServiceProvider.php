<?php

namespace App\Providers;

use App\Helpers\ResponseHelper;
use App\Repositories\BaseRepository;
use App\Repositories\BaseRepositoryInterface;
use App\Repositories\PlatformRepository;
use App\Repositories\PlatformRepositoryInterface;
use App\Repositories\PostRepository;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ResponseHelper::class, function ($app) {
            return new ResponseHelper();
        });
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(PlatformRepositoryInterface::class, PlatformRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
