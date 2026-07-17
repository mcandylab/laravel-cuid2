<?php

namespace Mcandylab\LaravelCuid2\Faker;

use Faker\Provider\Base;

class Cuid2Provider extends Base
{
    /**
     * Generate a new CUID2 for use in factories and seeders.
     *
     * @param  int|null  $length  Identifier length (4..32). null — take from config.
     * @param  string|null  $prefix  Optional Stripe-style prefix (e.g. "user").
     */
    public function cuid2(?int $length = null, ?string $prefix = null): string
    {
        return cuid2($length, $prefix);
    }
}
