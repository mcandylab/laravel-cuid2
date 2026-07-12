<?php

namespace Mcandylab\LaravelCuid2\Tests;

use Visus\Cuid2\Cuid2;

class HelperTest extends TestCase
{
    public function test_it_generates_a_valid_cuid2_with_default_length(): void
    {
        $id = cuid2();

        $this->assertSame(24, strlen($id));
        $this->assertTrue(Cuid2::isValid($id));
    }

    public function test_it_respects_explicit_length(): void
    {
        $id = cuid2(10);

        $this->assertSame(10, strlen($id));
        $this->assertTrue(Cuid2::isValid($id, 10));
    }

    public function test_it_reads_default_length_from_config(): void
    {
        config()->set('laravel-cuid2.length', 16);

        $this->assertSame(16, strlen(cuid2()));
    }

    public function test_it_generates_unique_values(): void
    {
        $this->assertNotSame(cuid2(), cuid2());
    }
}
