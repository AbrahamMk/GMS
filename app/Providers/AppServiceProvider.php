<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\BranchContext;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BranchContext::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant all permissions to authenticated admin/staff users
        Gate::before(function ($user, $ability) {
            return true;
        });
    }
}
