<?php

namespace Mcandylab\LaravelCuid2;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mcandylab\LaravelCuid2\Skeleton\SkeletonClass
 */
class LaravelCuid2Facade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-cuid2';
    }
}
