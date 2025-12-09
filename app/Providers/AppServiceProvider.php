<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

// Interfaces
use App\Repositories\Contracts\ChatRepositoryInterface;
use App\Repositories\Contracts\DoctorChatRepositoryInterface;

// Implementations
use App\Repositories\Eloquent\ChatRepository;
use App\Repositories\DoctorChatRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Patient Chat Repository
        $this->app->bind(
            ChatRepositoryInterface::class,
            ChatRepository::class
        );

        // Bind Doctor Chat Repository
        $this->app->bind(
            DoctorChatRepositoryInterface::class,
            DoctorChatRepository::class
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
