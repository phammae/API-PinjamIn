<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Interfaces\BookInterface;
use App\Contracts\Repositories\BookRepository;
use App\Contracts\Interfaces\AuthInterface;
use App\Contracts\Repositories\AuthRepository;
use App\Models\Book;
use App\Observers\BookObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Book Interface
        $this->app->bind(BookInterface::class, BookRepository::class);
        
        // Bind Auth Interface
        $this->app->bind(AuthInterface::class, AuthRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Book Observer
        Book::observe(BookObserver::class);
    }
}