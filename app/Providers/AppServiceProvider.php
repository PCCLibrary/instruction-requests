<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\InstructionRequestServiceInterface;
use App\Contracts\InstructionRequestDetailsServiceInterface;
use App\Services\InstructionRequestService;
use App\Services\InstructionRequestDetailsService;
use Livewire\Livewire;
use Illuminate\Support\Facades\Route;

/**
 * Service provider for binding interfaces to their concrete implementations
 * and registering application services.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This method is used to bind interfaces to their concrete implementations,
     * ensuring proper dependency injection and service resolution.
     *
     * @return void
     */
    public function register()
    {
        // Bind the InstructionRequestInterface to the InstructionRequestService
        $this->app->bind(InstructionRequestServiceInterface::class, InstructionRequestService::class);

        // Bind the InstructionRequestDetailsServiceInterface to the InstructionRequestDetailsService
        $this->app->bind(InstructionRequestDetailsServiceInterface::class, InstructionRequestDetailsService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * This method is used to perform actions required during application bootstrapping.
     *
     * @return void
     */
    public function boot()
    {
// Get the base path for the application
        $basePath = '/library/instruction-requests/public';

        // Configure Livewire assets
        Livewire::setScriptRoute(function ($handle) use ($basePath) {
            return Route::get($basePath . '/vendor/livewire/livewire.js', $handle)
                ->middleware('web')
                ->name('livewire.assets.scripts');
        });

        // Configure update endpoint
        Livewire::setUpdateRoute(function ($handle) use ($basePath) {
            return Route::post($basePath . '/livewire/update', $handle)
                ->middleware('web')
                ->name('livewire.assets.update');
        });

        // Force Livewire to use absolute paths
        config(['livewire.inject_assets' => true]);
    }
}
