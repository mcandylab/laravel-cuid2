<?php

namespace Mcandylab\LaravelCuid2;

use Visus\Cuid2\Cuid2;

class LaravelCuid2
{
    /**
     * Generate a new CUID2.
     *
     * @param  int|null  $length  Identifier length (4..32). null — take from config.
     */
    public function generate(?int $length = null): string
    {
        return cuid2($length);
    }

    /**
     * Check whether a string is a valid CUID2.
     *
     * @param  int|null  $expectedLength  Expected length (when an exact check is needed).
     */
    public function isValid(string $id, ?int $expectedLength = null): bool
    {
        return Cuid2::isValid($id, $expectedLength);
    }
}
