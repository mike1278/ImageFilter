<?php

namespace Modules\ImageFilter\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->mapRoutes();
    }

    protected function mapRoutes()
    {
        Route::group([], __DIR__.'/../Routes/web.php');
    }
}
