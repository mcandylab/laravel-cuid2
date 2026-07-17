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
     * When a prefix is given, a Stripe-style identifier `{prefix}_{cuid2}` is
     * returned. The cuid2 part stays fully spec-compliant; the prefix is just
     * an envelope around it. This is the single place where that envelope is
     * composed — the trait, Str macro and Faker provider all delegate here.
     *
     * @param  int|null  $length  Identifier length (4..32) of the cuid2 part.
     * @param  string|null  $prefix  Optional human-readable prefix (e.g. "user").
     */
    function cuid2(?int $length = null, ?string $prefix = null): string
    {
        if ($length === null) {
            $length = (function_exists('app') && app()->bound('config'))
                ? (int) config('laravel-cuid2.length', 24)
                : 24;
        }

        $id = (new Cuid2($length))->toString();

        return $prefix !== null && $prefix !== '' ? "{$prefix}_{$id}" : $id;
    }
}
