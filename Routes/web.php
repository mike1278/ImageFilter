<?php

use Modules\ImageFilter\Http\Controllers\ImageFilterCacheController;

Route::get(
    '/' . config('image-filter.route') . '/{file}',
    ImageFilterCacheController::class
)->name(config('image-filter.route_name'));
