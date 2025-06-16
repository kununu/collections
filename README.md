# Collections

The goal of this library is to provide some boilerplate code to assist you in creating more friendly collections when using `ArrayIterator`.

## Install

You can use this library by issuing the following command:

```bash
composer require kununu/collections
```

## Running Tests

Run the tests by doing:

```bash
composer install
vendor/bin/phpunit
```

or

```bash
composer install
composer test
```

To run test and generate coverage information:

```bash
composer install
composer test-coverage
```

## Usage

The library defines interfaces to deal with collections and also boilerplate code with default implementations.

You can either use the provided traits to your custom class extending `ArrayIterator` or simply expand the abstract collection classes using them.

It has a default implementation for a "basic" collection and also one to filter and group data on your collections (a "filterable" collection).

It defines interfaces to convert collection items to `array`, `string` and `int` and to compare items.

More details:

- [Collection Interfaces](docs/collection-interfaces.md)
- [Collection Trait](docs/collection-trait.md)
- [Filterable Collection Trait](docs/filterable-collection-trait.md)
- [Auto Sortable OffsetSet Trait](docs/autosortable-offsetset-trait.md)
- [Convertible](docs/convertible.md)
- [Abstract Collections](docs/abstract-collections.md)
- [Key Value](docs/key-value.md)
- [Abstract Item](docs/abstract-item.md)
- [Mapper](docs/mapper.md)

## Contribute

If you are interested in contributing read our [contributing guidelines](/CONTRIBUTING.md).

------------------------------

![Continuous Integration](https://github.com/kununu/collections/actions/workflows/continuous-integration.yml/badge.svg)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=kununu_collections&metric=alert_status)](https://sonarcloud.io/dashboard?id=kununu_collections)

