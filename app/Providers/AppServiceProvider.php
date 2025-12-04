<?php

namespace App\Providers;

use App\Repositories\Contracts\ChatRepositoryInterface;
use App\Repositories\Eloquent\ChatRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ChatRepositoryInterface::class,
            ChatRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
