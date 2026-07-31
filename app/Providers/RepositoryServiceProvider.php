<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Repository interface → Eloquent implementation bindings.
     * Add entries as repositories are implemented in Module 2+.
     *
     * @var array<class-string, class-string>
     */
    protected array $repositories = [
        // Example: \App\Repositories\Contracts\CityRepositoryInterface::class => \App\Repositories\Eloquent\CityRepository::class,
    ];

    /**
     * Register repository bindings.
     */
    public function register(): void
    {
        foreach ($this->repositories as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
