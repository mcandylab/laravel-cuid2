<?php

namespace Mcandylab\LaravelCuid2\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Visus\Cuid2\Cuid2 as Cuid2Generator;

class Cuid2 implements ValidationRule
{
    /**
     * @param  string|null  $prefix  When set, the value must be a Stripe-style `{prefix}_{cuid2}` identifier.
     */
    public function __construct(public ?string $prefix = null)
    {
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! static::isValid($value, $this->prefix)) {
            $fail('The :attribute must be a valid CUID2.');
        }
    }

    /**
     * Validate a value as a (optionally prefixed) CUID2.
     *
     * With a prefix, the value must start with `{prefix}_` and the remainder
     * must itself be a valid CUID2.
     */
    public static function isValid(mixed $value, ?string $prefix = null): bool
    {
        if (! is_string($value)) {
            return false;
        }

        if ($prefix !== null && $prefix !== '') {
            $needle = "{$prefix}_";

            if (! str_starts_with($value, $needle)) {
                return false;
            }

            $value = substr($value, strlen($needle));
        }

        return Cuid2Generator::isValid($value);
    }
}
