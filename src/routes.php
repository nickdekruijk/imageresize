<?php

Route::group(['middleware' => ['web']], function () {
    Route::get(rtrim(config('imageresize.route'), '/').'/{template}/{image}', 'NickDeKruijk\ImageResize\ResizeController@make')->where('image', '(.*)');
});
