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
        if ($path) {
            return asset(config('imageresize.route') . '/' . $template . '/' . $path, $secure);
        }
    }
}

if (!function_exists('asset_resized_encode')) {
    /**
     * Generate an resized urlencoded asset path for the application.
     *
     * @param  string  $template
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function asset_resized_encode($template, $path, $secure = null)
    {
        if ($path) {
            $path = str_replace('%2F', '/', rawurlencode($path));
            return asset(config('imageresize.route') . '/' . $template . '/' . $path, $secure);
        }
    }
}
