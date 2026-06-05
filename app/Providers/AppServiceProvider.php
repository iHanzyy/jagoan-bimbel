<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\FileMateri;
use App\Policies\MateriPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Gate::policy(FileMateri::class, MateriPolicy::class);
    }
}
