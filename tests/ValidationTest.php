<?php

namespace Mcandylab\LaravelCuid2\Tests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Mcandylab\LaravelCuid2\Rules\Cuid2;

class ValidationTest extends TestCase
{
    private function passes(mixed $value, array|string $rule): bool
    {
        return Validator::make(['id' => $value], ['id' => $rule])->passes();
    }

    // --- String rule: 'cuid2' ---

    public function test_string_rule_passes_for_valid_cuid2(): void
    {
        $this->assertTrue($this->passes(cuid2(), 'cuid2'));
    }

    public function test_string_rule_fails_for_invalid_value(): void
    {
        $this->assertFalse($this->passes('not-a-valid-cuid2!', 'cuid2'));
        $this->assertFalse($this->passes(12345, 'cuid2'));
    }

    public function test_string_rule_accepts_any_valid_length(): void
    {
        $this->assertTrue($this->passes(cuid2(10), 'cuid2'));
        $this->assertTrue($this->passes(cuid2(24), 'cuid2'));
        $this->assertTrue($this->passes(cuid2(32), 'cuid2'));
    }

    // --- Rule object: new Cuid2 ---

    public function test_rule_object_passes_and_fails(): void
    {
        $this->assertTrue($this->passes(cuid2(), [new Cuid2]));
        $this->assertFalse($this->passes('1nvalid', [new Cuid2]));
    }

    // --- Rule macro: Rule::cuid2() ---

    public function test_rule_macro_passes_and_fails(): void
    {
        $this->assertTrue($this->passes(cuid2(), [Rule::cuid2()]));
        $this->assertFalse($this->passes('1nvalid', [Rule::cuid2()]));
    }

    // --- Prefix: Rule object and macro ---

    public function test_rule_object_validates_prefix(): void
    {
        $this->assertTrue($this->passes(cuid2(prefix: 'user'), [new Cuid2('user')]));
        $this->assertFalse($this->passes(cuid2(prefix: 'admin'), [new Cuid2('user')]));
        $this->assertFalse($this->passes(cuid2(), [new Cuid2('user')]));
    }

    public function test_rule_macro_validates_prefix(): void
    {
        $this->assertTrue($this->passes(cuid2(prefix: 'user'), [Rule::cuid2(prefix: 'user')]));
        $this->assertFalse($this->passes(cuid2(prefix: 'admin'), [Rule::cuid2(prefix: 'user')]));
    }

    // --- Prefix: string rule 'cuid2:prefix' ---

    public function test_string_rule_validates_prefix(): void
    {
        $this->assertTrue($this->passes(cuid2(prefix: 'user'), 'cuid2:user'));
        $this->assertFalse($this->passes(cuid2(prefix: 'admin'), 'cuid2:user'));
        $this->assertFalse($this->passes(cuid2(), 'cuid2:user'));
    }

    // --- Error message ---

    public function test_error_message_mentions_cuid2(): void
    {
        $validator = Validator::make(['id' => '1nvalid'], ['id' => 'cuid2']);

        $this->assertStringContainsString('must be a valid CUID2', $validator->errors()->first('id'));
    }
}
