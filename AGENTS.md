# AGENTS.md

Steering notes for AI agents working in this repository. For install and usage see [README.md](README.md); for development setup, testing, and the contribution process see [CONTRIBUTING.md](CONTRIBUTING.md).

## What This Is

`kununu/collections` is a PHP library that reduces the boilerplate involved in building typed collections on top of `ArrayIterator`. It is consumed as a Composer package (`composer require kununu/collections`) and has no runtime application, service, or deployment.

## Domain

The library provides interfaces, traits, and abstract base classes for:

- Collections backed by `ArrayIterator` (basic and filterable).
- Converting collection items to and from `array`, `string`, `int`, and `Stringable`.
- Comparing collection items.
- Filtering and grouping collection data.
- Key/value structures, including enum-keyed variants.
- Building items and mapping between representations.

## Code Layout

- `src/` — library source, PSR-4 under `Kununu\Collection\`.
  - `Convertible/`, `Comparable/` — conversion and comparison interfaces.
  - `Filter/` — filtering and grouping primitives.
  - `EnumKeyValue/` — enum-keyed key/value support and its exceptions.
  - `AbstractItemBuilderTraits/`, `Mapper/`, `Helper/`, `Exception/` — item builders, mapping, helpers, and exceptions.
  - `TestCase/` — `AbstractCollectionTestCase` for consumers testing their own collections.
- `tests/` — PHPUnit tests, PSR-4 under `Kununu\Collection\Tests\`.
- `docs/` — per-feature documentation referenced from the README.

## Quality Gates

All of the following must pass. Run them via the Composer scripts:

- `composer cs` — PHP CS Fixer (kununu coding standards).
- `composer sniffer` — PHP_CodeSniffer.
- `composer phpstan` — PHPStan.
- `composer rector` — Rector (dry-run).
- `composer test` — PHPUnit.

See the `scripts` section of `composer.json` for the complete list.

## Hard Constraints

- The PHP version is pinned in `composer.json` (`require.php`); do not lower it.
- Keep the public API stable. This library follows semantic versioning; API changes require a corresponding `CHANGELOG.md` entry.
- Every code change must be covered by tests.
- Respect the existing coding standards; do not bypass the quality gates.
- Runtime code may depend only on PHP and `ext-mbstring`; keep other tooling in `require-dev`.

## Deep Documentation

Feature-level details live in `docs/` (collection interfaces, traits, convertibles, mappers, key/value, and the test case). Start from the links in [README.md](README.md).
