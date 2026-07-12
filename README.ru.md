# Laravel CUID2

[🇬🇧 English](README.md) | [🇷🇺 **Русский**](README.ru.md)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mcandylab/laravel-cuid2.svg?style=flat-square)](https://packagist.org/packages/mcandylab/laravel-cuid2)
[![Total Downloads](https://img.shields.io/packagist/dt/mcandylab/laravel-cuid2.svg?style=flat-square)](https://packagist.org/packages/mcandylab/laravel-cuid2)
[![run-tests](https://github.com/mcandylab/laravel-cuid2/actions/workflows/main.yml/badge.svg)](https://github.com/mcandylab/laravel-cuid2/actions/workflows/main.yml)

Использование [CUID2](https://github.com/paralleldrive/cuid2) в качестве первичных ключей
Eloquent-моделей в Laravel. Пакет предоставляет трейт для модели, глобальный хелпер `cuid2()`
и schema-макросы для миграций. Генерация делегируется библиотеке
[`visus/cuid2`](https://github.com/visus-io/php-cuid2).

## Требования

- PHP >= 8.2
- Laravel 12 или 13 (Laravel 13 требует PHP 8.3+)

## Установка

```bash
composer require mcandylab/laravel-cuid2
```

Пакет использует авто-обнаружение (auto-discovery). При необходимости опубликуйте конфиг:

```bash
php artisan vendor:publish --provider="Mcandylab\LaravelCuid2\LaravelCuid2ServiceProvider" --tag="config"
```

## Использование

### Трейт для модели

Подключите трейт `HasCuid2` — первичный ключ будет автоматически заполняться
валидным CUID2 при создании записи:

```php
use Illuminate\Database\Eloquent\Model;
use Mcandylab\LaravelCuid2\Concerns\HasCuid2;

class Post extends Model
{
    use HasCuid2;
}
```

Трейт сам выставляет `keyType = 'string'` и `incrementing = false`.
Чтобы генерировать cuid2 не только для первичного ключа, переопределите `uniqueIds()`:

```php
public function uniqueIds(): array
{
    return [$this->getKeyName(), 'public_id'];
}
```

### Миграции

Макросы `cuid2()` и `foreignCuid2()` объявляют `char`-колонки нужной длины:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->cuid2()->primary();      // колонка id
    $table->string('title');
    $table->timestamps();
});

Schema::create('comments', function (Blueprint $table) {
    $table->cuid2()->primary();
    $table->foreignCuid2('post_id')->constrained();
    $table->text('body');
});
```

### Хелпер

```php
$id = cuid2();      // 24 символа (или значение config('laravel-cuid2.length'))
$short = cuid2(10); // произвольная длина 4..32
```

### Фасад

```php
use Mcandylab\LaravelCuid2\LaravelCuid2Facade as Cuid2;

Cuid2::generate();          // сгенерировать id
Cuid2::isValid($someId);    // проверить строку
```

## Конфигурация

`config/laravel-cuid2.php`:

```php
return [
    // Длина идентификатора (4..32). Стандарт cuid2 — 24.
    'length' => (int) env('CUID2_LENGTH', 24),
];
```

## Тестирование

```bash
composer test
```

## Changelog

См. [CHANGELOG](CHANGELOG.md).

## Contributing

См. [CONTRIBUTING](CONTRIBUTING.md).

## Безопасность

Если вы обнаружили проблему безопасности, пожалуйста, [создайте issue](https://github.com/mcandylab/laravel-cuid2/issues).

## Авторы

- [Andrey Abramov](https://github.com/mcandylab)
- [All Contributors](../../contributors)
- [visus-io/php-cuid2](https://github.com/visus-io/php-cuid2)

## Лицензия

The MIT License (MIT). См. [License File](LICENSE.md).
