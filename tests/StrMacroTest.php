<?php

namespace Mcandylab\LaravelCuid2\Tests;

use Illuminate\Support\Str;
use Visus\Cuid2\Cuid2;

class StrMacroTest extends TestCase
{
    // --- Str::cuid2() ---

    public function test_it_generates_a_valid_cuid2_with_default_length(): void
    {
        $id = Str::cuid2();

        $this->assertSame(24, strlen($id));
        $this->assertTrue(Cuid2::isValid($id));
    }

    public function test_it_respects_explicit_length(): void
    {
        $id = Str::cuid2(10);

        $this->assertSame(10, strlen($id));
        $this->assertTrue(Cuid2::isValid($id, 10));
    }

    public function test_it_reads_default_length_from_config(): void
    {
        config()->set('laravel-cuid2.length', 16);

        $this->assertSame(16, strlen(Str::cuid2()));
    }

    public function test_it_generates_unique_values(): void
    {
        $this->assertNotSame(Str::cuid2(), Str::cuid2());
    }

    // --- Str::isCuid2() ---

    public function test_it_validates_a_valid_cuid2(): void
    {
        $this->assertTrue(Str::isCuid2(cuid2()));
    }

    public function test_it_rejects_invalid_values(): void
    {
        $this->assertFalse(Str::isCuid2('not-a-valid-cuid2!'));
        $this->assertFalse(Str::isCuid2('1nvalid'));
    }

    public function test_it_rejects_non_string_values(): void
    {
        $this->assertFalse(Str::isCuid2(12345));
        $this->assertFalse(Str::isCuid2(null));
    }

    public function test_it_accepts_any_valid_length(): void
    {
        $this->assertTrue(Str::isCuid2(cuid2(10)));
        $this->assertTrue(Str::isCuid2(cuid2(32)));
    }

    // --- Prefix support ---

    public function test_it_generates_a_prefixed_id(): void
    {
        $id = Str::cuid2(prefix: 'user');

        $this->assertStringStartsWith('user_', $id);
        $this->assertTrue(Cuid2::isValid(substr($id, 5)));
    }

    public function test_it_validates_a_prefixed_id(): void
    {
        $this->assertTrue(Str::isCuid2(Str::cuid2(prefix: 'user'), 'user'));
        $this->assertFalse(Str::isCuid2(Str::cuid2(prefix: 'admin'), 'user'));
        $this->assertFalse(Str::isCuid2(cuid2(), 'user'));
    }
}
