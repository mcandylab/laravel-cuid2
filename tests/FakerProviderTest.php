<?php

namespace Mcandylab\LaravelCuid2\Tests;

use Visus\Cuid2\Cuid2;

class FakerProviderTest extends TestCase
{
    public function test_it_generates_a_valid_cuid2_with_default_length(): void
    {
        $id = fake()->cuid2();

        $this->assertSame(24, strlen($id));
        $this->assertTrue(Cuid2::isValid($id));
    }

    public function test_it_respects_explicit_length(): void
    {
        $id = fake()->cuid2(10);

        $this->assertSame(10, strlen($id));
        $this->assertTrue(Cuid2::isValid($id, 10));
    }

    public function test_it_generates_unique_values(): void
    {
        $this->assertNotSame(fake()->cuid2(), fake()->cuid2());
    }

    public function test_it_generates_a_prefixed_id(): void
    {
        $id = fake()->cuid2(prefix: 'user');

        $this->assertStringStartsWith('user_', $id);
        $this->assertTrue(Cuid2::isValid(substr($id, 5)));
    }
}
