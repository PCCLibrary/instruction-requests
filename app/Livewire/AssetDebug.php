<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Route;

class AssetDebug extends Component
{
    public function render()
    {
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return str_contains($route->uri(), 'livewire');
        })->map(function ($route) {
            return [
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'name' => $route->getName()
            ];
        })->values();

        return view('livewire.asset-debug', [
            'routes' => $routes,
            'baseUrl' => url('/'),
            'assetUrl' => config('livewire.asset_url'),
            'publicPath' => public_path(),
            'appUrl' => config('app.url'),
            'headers' => getallheaders(),
            'livewireConfig' => [
                'url' => config('livewire.asset_url'),
                'path' => config('livewire.path'),
                'middleware_group' => config('livewire.middleware_group'),
                'manifest_path' => config('livewire.manifest_path'),
            ]
        ]);
    }
}
