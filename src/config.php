<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Route
    |--------------------------------------------------------------------------
    |
    | The route to watch for. Must be in public_path()
    | A route will be created like: media/{template}/{image}
    | Where {template} must match one of the templates below.
    | And {image} must match a valid image file inside the 'originals' folder.
    | Be aware that the is not a file directly matching this route.
    |
    */

    'route' => 'media/resized',

    /*
    |--------------------------------------------------------------------------
    | Originals folder
    |--------------------------------------------------------------------------
    |
    | The path to the original images.
    |
    */

    'originals' => 'media',

    /*
    |--------------------------------------------------------------------------
    | Template presets
    |--------------------------------------------------------------------------
    |
    | type: crop or fit
    | quality: jpeg quality in percentage (0 - 100)
    | width: image width (or maximum width when type is 'fit')
    | height: image height (or maximum height when type is 'fit')
    | grayscale: when true apply IMG_FILTER_GRAYSCALE filter
    | blur: use IMG_FILTER_GAUSSIAN_BLUR filter (higher value is stronger blur)
    |
    */

    'templates' => [
        'thumbnail' => [
            'type' => 'crop',
            'quality' => 70,
            'width' => 180,
            'height' => 180,
        ],
        'medium' => [
            'type' => 'crop',
            'quality' => 60,
            'width' => 612,
            'height' => 408,
        ],
        'large' => [
            'type' => 'fit',
            'quality' => 70,
            'width' => 1600,
            'height' => 1600,
        ],
        'blur' => [
            'type' => 'fit',
            'blur' => 30,
            'width' => 300,
            'height' => 200,
        ],
        'gray' => [
            'type' => 'fit',
            'grayscale' => true,
            'width' => 300,
            'height' => 200,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | quality_jpeg
    |--------------------------------------------------------------------------
    |
    | The default JPEG quality when template doesn't specify it
    |
    */

    'quality_jpeg' => 80,

];