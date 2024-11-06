<?php

// if (!function_exists('app_path')) {
//     function app_path($path = '')
//     {
//         return __DIR__ . '/../' . $path;
//     }
// }

if (!function_exists('app_path')) {
    function app_path($path = '')
    {
        return app()->basePath('app') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

