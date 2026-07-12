<?php

namespace Mcandylab\LaravelCuid2\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Visus\Cuid2\Cuid2 as Cuid2Generator;

class Cuid2 implements ValidationRule
{
    /**
     * @param  int|null  $length  Expected exact length (4..32). null accepts any valid length.
     */
    public function __construct(public ?int $length = null)
    {
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! Cuid2Generator::isValid($value, $this->length)) {
            $fail('The :attribute must be a valid CUID2.');
        }
    }
}
