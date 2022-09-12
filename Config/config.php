<?php

use Modules\ImageFilter\Filter\Colorize;
use Modules\ImageFilter\Filter\Encode;
use Modules\ImageFilter\Filter\Resize;
use Modules\ImageFilter\Filter\Square;

return [
    /*
    |--------------------------------------------------------------------------
    | Name of route
    |--------------------------------------------------------------------------
    |
    | Enter the routes name to enable dynamic image-filter manipulation.
    | This handle will define the first part of the URI:
    |
    | {route}/{filename}
    |
    */
    'route' => 'images',

    'route_name' => 'image-filter',

    /*
    | If cache the images
    */
    'cache_active' => env('IMAGE_CACHE_ACTIVE', false),

    /*
    |--------------------------------------------------------------------------
    | Image Cache Lifetime
    |--------------------------------------------------------------------------
    |
    | Lifetime in minutes of the images handled by the image-filter route.
    |
    */
    'lifetime' => 43200,

    /*
    | driver cache
    */
    'cache_driver' => env('IMAGE_CACHE_DRIVER', false),

    /*
    |--------------------------------------------------------------------------
    | LocalStorage paths
    |--------------------------------------------------------------------------
    |
    | The following paths will be searched for the image filename, submitted
    | by URI.
    |
    | Define as many directories as you like.
    |
    */
    'paths' => [
        storage_path('app/public'),
    ],

    /*
    | active filters
    | all class, need implement the interface Modules\ImageFilter\Filter\Filter
    */
    'filters' => [
        'square' => Square::class,
        /*
        | parameters: width, height, aspectRatio, upsize
        | example: 200,,false, true | ,300,true,false | 300 | ,200
        */
        'resize' => Resize::class,
        /*
        | parameters: red, green, blue
        | example: 100,0,50 | 0,-50 | 40
        */
        'colorize' => Colorize::class,
        /*
        | parameters: encode, quality
        | example: jpg,90 | webp | png,100
        */
        'encode' => Encode::class,
    ],

    /*
     | By default the package will throw an `InvalidFilterQuery` exception when a filter in the
     | URL is not allowed in the `allowedFilters()` method.
     */
    'disable_invalid_filter_query_exception' => false,

    /*
     | By default the package inspects query string of request using $request->query().
     | You can change this behavior to inspect the request body using $request->input()
     | by setting this value to `body`.
     |
     | Possible values: `query_string`, `body`
     */
    'request_data_source' => 'body',
];
