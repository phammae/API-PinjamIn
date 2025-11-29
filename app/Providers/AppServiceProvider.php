<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Interfaces\AuthInterface;
use App\Contracts\Repositories\AuthRepository;
use App\Models\Movie;
use App\Observers\MovieObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        
        // Bind Auth Interface
        $this->app->bind(AuthInterface::class, AuthRepository::class);
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