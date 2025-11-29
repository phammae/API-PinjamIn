<?php

namespace App\Providers;

use App\Models\Movie;
use App\Observers\MovieObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Movie::observe(MovieObserver::class); // ✅ Tambahkan ini
    }
}