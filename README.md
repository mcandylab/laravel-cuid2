# Laravel CUID2

[🇬🇧 **English**](README.md) | [🇷🇺 Русский](README.ru.md)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mcandylab/laravel-cuid2.svg?style=flat-square)](https://packagist.org/packages/mcandylab/laravel-cuid2)
[![Total Downloads](https://img.shields.io/packagist/dt/mcandylab/laravel-cuid2.svg?style=flat-square)](https://packagist.org/packages/mcandylab/laravel-cuid2)
[![run-tests](https://github.com/mcandylab/laravel-cuid2/actions/workflows/main.yml/badge.svg)](https://github.com/mcandylab/laravel-cuid2/actions/workflows/main.yml)

Use [CUID2](https://github.com/paralleldrive/cuid2) as primary keys for your Eloquent
models in Laravel. The package provides a model trait, a global `cuid2()` helper and
schema macros for migrations. Generation is delegated to the
[`visus/cuid2`](https://github.com/visus-io/php-cuid2) library.

## Requirements

- PHP >= 8.2
- Laravel 12 or 13 (Laravel 13 requires PHP 8.3+)

## Installation

```bash
composer require mcandylab/laravel-cuid2
```

The package uses auto-discovery. Publish the config if needed:

```bash
php artisan vendor:publish --provider="Mcandylab\LaravelCuid2\LaravelCuid2ServiceProvider" --tag="config"
```

## Usage

### Model trait

Add the `HasCuid2` trait — the primary key will be automatically populated with a
valid CUID2 when a record is created:

```php
use Illuminate\Database\Eloquent\Model;
use Mcandylab\LaravelCuid2\Concerns\HasCuid2;

class Post extends Model
{
    use HasCuid2;
}
```

The trait sets `keyType = 'string'` and `incrementing = false` for you.
To generate a cuid2 for more than just the primary key, override `uniqueIds()`:

```php
public function uniqueIds(): array
{
    return [$this->getKeyName(), 'public_id'];
}
```

### Migrations

The `cuid2()` and `foreignCuid2()` macros declare `varchar` columns (Laravel's
`Schema::defaultStringLength`, 255 by default). They are fully indexable across
databases and fit any value the package generates, including prefixed
`{prefix}_{cuid2}` identifiers:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->cuid2()->primary();      // id column
    $table->string('title');
    $table->timestamps();
});

Schema::create('comments', function (Blueprint $table) {
    $table->cuid2()->primary();
    $table->foreignCuid2('post_id')->constrained();
    $table->text('body');
});
```

For polymorphic relations use `cuid2Morphs()` (and `nullableCuid2Morphs()`),
the CUID2 counterparts of Laravel's `ulidMorphs()`. They add a `{name}_type`
string column, a `{name}_id` varchar column and a composite index:

```php
Schema::create('tokens', function (Blueprint $table) {
    $table->cuid2()->primary();
    $table->cuid2Morphs('tokenable');          // tokenable_type + tokenable_id
    $table->string('token');
});

// nullable variant
$table->nullableCuid2Morphs('tokenable');
```

### Helper

```php
$id = cuid2();      // 24 characters (or config('laravel-cuid2.length'))
$short = cuid2(10); // exactly 10 characters (4..32 allowed)
```

### Facade

```php
use Mcandylab\LaravelCuid2\LaravelCuid2Facade as Cuid2;

Cuid2::generate();          // generate an id
Cuid2::isValid($someId);    // validate a string
```

### Str macros

Aligned with the core `Str::uuid()` / `Str::ulid()` helpers:

```php
use Illuminate\Support\Str;

Str::cuid2();          // generate (respects config('laravel-cuid2.length'))
Str::cuid2(10);        // exactly 10 characters (4..32 allowed)
Str::isCuid2($value);  // validate a value (false for non-strings)
```

### Faker

A `cuid2()` Faker formatter is available for factories and seeders:

```php
use App\Models\Post;

Post::factory()->create(['id' => fake()->cuid2()]);

fake()->cuid2();   // generate (respects config('laravel-cuid2.length'))
fake()->cuid2(10); // exactly 10 characters (4..32 allowed)
```

### Validation

The `cuid2` rule validates that a value is a well-formed CUID2 of any valid length
(4..32). It is available in three forms:

```php
use Illuminate\Validation\Rule;
use Mcandylab\LaravelCuid2\Rules\Cuid2;

$request->validate([
    'id'    => 'cuid2',          // string rule
    'ref'   => [new Cuid2],      // rule object
    'ext'   => [Rule::cuid2()],  // rule macro
]);
```

### Prefixed identifiers (Stripe-style)

You can produce human-readable identifiers such as `user_p6p168tx…` by declaring
a `$cuid2Prefix` property on the model. The prefix is just an envelope — the
cuid2 part after the `_` stays fully spec-compliant:

```php
class User extends Model
{
    use HasCuid2;

    protected string $cuid2Prefix = 'user';
}
```

No special column type is needed: `cuid2()` and `foreignCuid2()` already create a
`varchar` that fits any `{prefix}_{cuid2}` value:

```php
Schema::create('users', function (Blueprint $table) {
    $table->cuid2()->primary();
    $table->string('name');
});

// a foreign key referencing a prefixed model
$table->foreignCuid2('user_id');
```

The generators accept an optional prefix too:

```php
cuid2(prefix: 'user');            // user_…
Str::cuid2(prefix: 'user');       // user_…
fake()->cuid2(prefix: 'user');    // user_…
```

Validation can optionally check the prefix — it verifies the `{prefix}_` and
that the remainder is a valid CUID2. Without a prefix the rule is unchanged:

```php
$request->validate([
    'id'  => 'cuid2:user',                 // string rule
    'id2' => [new Cuid2('user')],          // rule object
    'id3' => [Rule::cuid2(prefix: 'user')], // rule macro
]);

Str::isCuid2($value, 'user');              // and via the Str macro
```

> **Note:** `cuid2Morphs()` / `nullableCuid2Morphs()` take no prefix argument — a
> polymorphic column may point at models with different prefixes, and the prefix
> is already implied by the `{name}_type` column. Like every id column in this
> package, `{name}_id` is a `varchar`, so a prefixed model can safely be a
> polymorphic target without truncation.

## Configuration

`config/laravel-cuid2.php`:

```php
return [
    // Identifier length (4..32). The cuid2 standard is 24.
    'length' => (int) env('CUID2_LENGTH', 24),
];
```

`length` only affects generation (`cuid2()`, `Str::cuid2()`, `fake()->cuid2()`,
`HasCuid2`). Schema macros are unaffected — they always declare a `varchar`, so
changing the length does not require a migration.

## Testing

```bash
composer test
```

## Changelog

See [CHANGELOG](CHANGELOG.md).

## Contributing

See [CONTRIBUTING](CONTRIBUTING.md).

## Security

If you discover any security related issues, please [open an issue](https://github.com/mcandylab/laravel-cuid2/issues).

## Credits

- [Andrey Abramov](https://github.com/mcandylab)
- [All Contributors](../../contributors)
- [visus-io/php-cuid2](https://github.com/visus-io/php-cuid2)

## License

The MIT License (MIT). See [License File](LICENSE.md).
