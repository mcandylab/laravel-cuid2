<?php

use Visus\Cuid2\Cuid2;

if (! function_exists('cuid2')) {
    /**
     * Generate a new CUID2.
     *
     * If no length is provided, it is taken from the laravel-cuid2.length
     * config value (falling back to 24 when the application container is not
     * yet booted).
     *
     * @param  int|null  $length  Identifier length (4..32).
     */
    function cuid2(?int $length = null): string
    {
        if ($length === null) {
            $length = (function_exists('app') && app()->bound('config'))
                ? (int) config('laravel-cuid2.length', 24)
                : 24;
        }

        return (new Cuid2($length))->toString();
    }
}
