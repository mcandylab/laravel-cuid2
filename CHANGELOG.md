# Changelog

All notable changes to `laravel-cuid2` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 1.4.0 - 2026-07-17

### Added

- Stripe-style prefixed identifiers (`user_p6p168tx…`): declare a `$cuid2Prefix`
  property on a model to have `HasCuid2` produce `{prefix}_{cuid2}` keys. The
  cuid2 part stays fully spec-compliant.
- Optional `prefix` argument on the `cuid2()` helper, `Str::cuid2()` /
  `Str::isCuid2()` macros and the `fake()->cuid2()` formatter.
- `cuid2WithPrefix()` and `foreignCuid2WithPrefix()` schema blueprint macros that
  create a `varchar` column (Laravel's `Schema::defaultStringLength`) able to hold
  the whole prefixed value.
- Optional prefix validation via `Rule::cuid2(prefix: …)`, `new Cuid2(…)`,
  `Str::isCuid2($value, …)` and the string rule `cuid2:<prefix>`.

### Changed

- `cuid2Morphs()` / `nullableCuid2Morphs()` now declare the `{name}_id` column as
  `varchar` instead of a fixed-length `char`, so a prefixed model can be used as a
  polymorphic target without truncation.

### Removed

- The length argument of the `cuid2` validation rule (`cuid2:<length>`,
  `new Cuid2(<length>)`, `Rule::cuid2(length: …)`, `Str::isCuid2($value, <length>)`).
  The rule never read `config('laravel-cuid2.length')`, so the length had to be
  restated by hand; it now checks the CUID2 format (any valid length, 4..32) and,
  optionally, the prefix. Generation is unaffected — `cuid2()`, `Str::cuid2()`,
  `fake()->cuid2()` and the schema macros still take a length.

## 1.3.0 - 2026-07-16

### Added

- `Str::cuid2()` and `Str::isCuid2()` macros, aligning with the core `Str::uuid()` /
  `Str::ulid()` helpers for generating and validating identifiers.
- `fake()->cuid2()` Faker formatter for use in factories and seeders.

## 1.2.0 - 2026-07-12

### Added

- `cuid2` validation rule to check that a value is a well-formed CUID2, available
  as a string rule (`'cuid2'` / `'cuid2:10'` for an exact length), a rule object
  (`Mcandylab\LaravelCuid2\Rules\Cuid2`) and a `Rule::cuid2()` macro.

## 1.1.0 - 2026-07-12

### Added

- `cuid2Morphs()` and `nullableCuid2Morphs()` schema blueprint macros for
  polymorphic relations, the CUID2 counterparts of Laravel's `ulidMorphs()`.

## 1.0.0 - 2026-07-12

### Added

- `HasCuid2` trait that auto-populates a model's primary key with a CUID2 on create,
  sets `keyType` to `string` and disables auto-incrementing, and supports additional
  columns via `uniqueIds()`.
- Global `cuid2()` helper for generating identifiers, with a configurable length.
- `cuid2()` and `foreignCuid2()` schema blueprint macros for migrations.
- `LaravelCuid2` facade with `generate()` and `isValid()` methods.
- Publishable configuration file with a `length` option (default `24`).
- Support for PHP 8.2+ and Laravel 12 and 13 (Laravel 13 requires PHP 8.3+).

Generation is powered by the [`visus/cuid2`](https://github.com/visus-io/php-cuid2) library.
