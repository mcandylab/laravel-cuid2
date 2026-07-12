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

The `cuid2()` and `foreignCuid2()` macros declare `char` columns of the configured length:

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

### Helper

```php
$id = cuid2();      // 24 characters (or config('laravel-cuid2.length'))
$short = cuid2(10); // arbitrary length 4..32
```

### Facade

```php
use Mcandylab\LaravelCuid2\LaravelCuid2Facade as Cuid2;

Cuid2::generate();          // generate an id
Cuid2::isValid($someId);    // validate a string
```

## Configuration

`config/laravel-cuid2.php`:

```php
return [
    // Identifier length (4..32). The cuid2 standard is 24.
    'length' => (int) env('CUID2_LENGTH', 24),
];
```

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
