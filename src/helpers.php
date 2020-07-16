<?php

if (!function_exists('asset_resized')) {
    /**
     * Generate an resized asset path for the application.
     *
     * @param  string  $template
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function asset_resized($template, $path, $secure = null)
    {
        return asset(config('imageresize.route') . '/' . $template . '/' . $path, $secure);
    }
}
