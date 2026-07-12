<?php

namespace Mcandylab\LaravelCuid2\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class BlueprintMacroTest extends TestCase
{
    private function blueprint(string $table): Blueprint
    {
        $connection = DB::connection();
        $connection->useDefaultSchemaGrammar();

        return new Blueprint($connection, $table);
    }

    public function test_cuid2_macro_creates_char_column_with_configured_length(): void
    {
        config()->set('laravel-cuid2.length', 24);

        $column = $this->blueprint('posts')->cuid2('id');

        $this->assertSame('id', $column->get('name'));
        $this->assertSame('char', $column->get('type'));
        $this->assertSame(24, $column->get('length'));
    }

    public function test_cuid2_macro_respects_config_length(): void
    {
        config()->set('laravel-cuid2.length', 12);

        $column = $this->blueprint('posts')->cuid2('id');

        $this->assertSame(12, $column->get('length'));
    }

    public function test_foreign_cuid2_macro_creates_char_foreign_column(): void
    {
        config()->set('laravel-cuid2.length', 24);

        $column = $this->blueprint('comments')->foreignCuid2('post_id');

        $this->assertSame('post_id', $column->get('name'));
        $this->assertSame('char', $column->get('type'));
        $this->assertSame(24, $column->get('length'));
    }
}
