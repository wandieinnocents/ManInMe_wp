<?php

// scoper-autoload.php @generated by PhpScoper

$loader = require_once __DIR__.'/autoload.php';

// Exposed functions. For more information see:
// https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#exposing-functions
if (!function_exists('composerRequire16190c9716d5de710cb9f2d881ca53b6')) {
    function composerRequire16190c9716d5de710cb9f2d881ca53b6() {
        return \SmashBalloon\YoutubeFeed\Vendor\composerRequire16190c9716d5de710cb9f2d881ca53b6(...func_get_args());
    }
}
if (!function_exists('get_plugins')) {
    function get_plugins() {
        return \SmashBalloon\YoutubeFeed\Vendor\get_plugins(...func_get_args());
    }
}
if (!function_exists('dbDelta')) {
    function dbDelta() {
        return \SmashBalloon\YoutubeFeed\Vendor\dbDelta(...func_get_args());
    }
}
if (!function_exists('trailingslashit')) {
    function trailingslashit() {
        return \SmashBalloon\YoutubeFeed\Vendor\trailingslashit(...func_get_args());
    }
}
if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url() {
        return \SmashBalloon\YoutubeFeed\Vendor\plugin_dir_url(...func_get_args());
    }
}

return $loader;
