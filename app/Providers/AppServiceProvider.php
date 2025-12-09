<?php

namespace App\Providers;

use App\Repositories\Contracts\ChatRepositoryInterface;
use App\Repositories\Eloquent\ChatRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        RateLimiter::for('resend-otp', function (Request $request) {
            return Limit::perMinute(2)->by($request->ip());
        });
    }
}
