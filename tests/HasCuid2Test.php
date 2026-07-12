<?php

namespace Mcandylab\LaravelCuid2\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mcandylab\LaravelCuid2\Concerns\HasCuid2;
use Visus\Cuid2\Cuid2;

class HasCuid2Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('posts', function (Blueprint $table) {
            $table->cuid2()->primary();
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->cuid2()->primary();
            $table->foreignCuid2('post_id')->constrained();
            $table->text('body');
            $table->timestamps();
        });
    }

    public function test_foreign_cuid2_column_stores_related_key(): void
    {
        $post = TestPost::create(['title' => 'Parent']);

        $comment = TestComment::create([
            'post_id' => $post->getKey(),
            'body' => 'Nice',
        ]);

        $this->assertTrue(Cuid2::isValid($comment->getKey()));
        $this->assertSame($post->getKey(), $comment->post_id);
    }

    public function test_it_auto_populates_a_valid_cuid2_primary_key(): void
    {
        $post = TestPost::create(['title' => 'Hello']);

        $this->assertNotEmpty($post->getKey());
        $this->assertTrue(Cuid2::isValid($post->getKey()));
    }

    public function test_key_is_non_incrementing_string(): void
    {
        $post = new TestPost();

        $this->assertFalse($post->getIncrementing());
        $this->assertSame('string', $post->getKeyType());
    }

    public function test_it_does_not_overwrite_an_explicit_key(): void
    {
        $explicit = cuid2();

        $post = TestPost::create(['id' => $explicit, 'title' => 'Fixed']);

        $this->assertSame($explicit, $post->getKey());
    }

    public function test_records_are_retrievable_by_string_key(): void
    {
        $post = TestPost::create(['title' => 'Findable']);

        $this->assertTrue($post->is(TestPost::find($post->getKey())));
    }
}

class TestPost extends Model
{
    use HasCuid2;

    protected $table = 'posts';

    protected $guarded = [];
}

class TestComment extends Model
{
    use HasCuid2;

    protected $table = 'comments';

    protected $guarded = [];
}
