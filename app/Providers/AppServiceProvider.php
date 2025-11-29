<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Interfaces\AuthInterface;
use App\Contracts\Interfaces\MovieInterface;
use App\Contracts\Repositories\AuthRepository;
use App\Contracts\Repositories\MovieRepository;
use App\Models\Movie;
use App\Observers\MovieObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthInterface::class, AuthRepository::class);
        $this->app->bind(MovieInterface::class, MovieRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Book Observer
        Movie::observe(MovieObserver::class);
    }
}