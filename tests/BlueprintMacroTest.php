<?php

namespace Mcandylab\LaravelCuid2\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
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

    public function test_cuid2_morphs_macro_creates_type_and_char_id_columns(): void
    {
        config()->set('laravel-cuid2.length', 24);

        $blueprint = $this->blueprint('taggables');
        $blueprint->cuid2Morphs('taggable');

        $columns = collect($blueprint->getColumns());

        $type = $columns->firstWhere('name', 'taggable_type');
        $this->assertNotNull($type);
        $this->assertSame('string', $type->get('type'));

        $id = $columns->firstWhere('name', 'taggable_id');
        $this->assertNotNull($id);
        $this->assertSame('string', $id->get('type'));
        $this->assertSame(Builder::$defaultStringLength, $id->get('length'));
        $this->assertNull($id->get('nullable'));

        $index = collect($blueprint->getCommands())->firstWhere('name', 'index');
        $this->assertNotNull($index);
        $this->assertSame(['taggable_type', 'taggable_id'], $index->get('columns'));
    }

    public function test_cuid2_with_prefix_macro_creates_a_varchar_column(): void
    {
        $column = $this->blueprint('users')->cuid2WithPrefix('id');

        $this->assertSame('id', $column->get('name'));
        $this->assertSame('string', $column->get('type'));
        $this->assertSame(Builder::$defaultStringLength, $column->get('length'));
    }

    public function test_foreign_cuid2_with_prefix_macro_creates_a_varchar_foreign_column(): void
    {
        $column = $this->blueprint('posts')->foreignCuid2WithPrefix('user_id');

        $this->assertSame('user_id', $column->get('name'));
        $this->assertSame('string', $column->get('type'));
        $this->assertSame(Builder::$defaultStringLength, $column->get('length'));
    }

    public function test_nullable_cuid2_morphs_macro_creates_nullable_columns(): void
    {
        config()->set('laravel-cuid2.length', 24);

        $blueprint = $this->blueprint('taggables');
        $blueprint->nullableCuid2Morphs('taggable');

        $columns = collect($blueprint->getColumns());

        $type = $columns->firstWhere('name', 'taggable_type');
        $this->assertTrue($type->get('nullable'));

        $id = $columns->firstWhere('name', 'taggable_id');
        $this->assertSame('string', $id->get('type'));
        $this->assertSame(Builder::$defaultStringLength, $id->get('length'));
        $this->assertTrue($id->get('nullable'));
    }
}
