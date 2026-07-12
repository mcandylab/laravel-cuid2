# Changelog

All notable changes to `laravel-cuid2` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
