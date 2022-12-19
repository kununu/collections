# Collections

The goal of this library is to provide some boilerplate code to assist you in creating more friendly collections when using `ArrayIterator`.

## Install

#### Add custom private repositories to composer.json

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/kununu/collections.git",
      "no-api": true
    },
    {
      "type": "vcs",
      "url": "https://github.com/kununu/kununu-scripts.git",
      "no-api": true
    }
  ]
}
```

#### Require package

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

## Usage

The library provide three traits that you can add to your custom class extending `ArrayIterator`.

It defines interfaces to convert collection items to `array`, `string` and `int` and to compare items.

It also provides some interfaces to filter and group data on your collections and base classes with default implementations.

More details:

- [Collection Trait](docs/collection-trait.md)
- [Filterable Collection Trait](docs/filterable-collection-trait.md)
- [Auto Sortable OffsetSet Trait](docs/autosortable-offsetset-trait.md)
- [Convertible](docs/convertible.md)
- [Abstract Collections](docs/abstract-collections.md)
- [Abstract Item](docs/abstract-item.md)
- [Mapper](docs/mapper.md)
