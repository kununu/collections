# Changelog

All notable changes to `kununu/collections` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Entries before this file existed were reconstructed from the [git tags](https://github.com/kununu/collections/tags) and PR history, so older releases are summarized rather than exhaustive.

## [Unreleased]

_No unreleased changes yet._

## [8.0.0] - 2026-06-22

Major release. See **Backwards compatibility** below before upgrading.

### Added

- `Collection::intersect(self $other): self|static`
  - Returns a new collection with the intersection between two collections of the same type (throws `NotSameCollectionTypeException` if the other collection is not the same type).
- `Collection::merge(self ...$others): self|static`
  - Returns a new collection with the items of this collection followed by the items of the given ones.
  - Items are added through the collection's own `append`, so any overridden append logic (validation, transformation, keying) is honored.
  - Same-type guard as `diff`/`intersect`.
- `Collection::collectionOrNull(): self|static|null`
  - Returns `null` when the collection is empty, otherwise the collection itself.
- `Exception\NotSameCollectionTypeException`
  - Thrown by `diff`, `intersect`, and `merge` when the other collection is not the same type
  - Extends `InvalidArgumentException`, so existing `catch (InvalidArgumentException)` handlers keep working.
- `AbstractBasicItem::getAllProperties` is now cached per class, instead of being recomputed on each `new` call.
- `KeyValueInterface` - a shared interface for key-value containers, implemented by both `KeyValue` and `EnumKeyValue\AbstractEnumKeyValue`.
- `KeyValueTrait` - extracts the shared storage, iteration, and `ArrayAccess` behavior of the key-value containers so it can be reused by custom implementations.
- `EnumKeyValue\AbstractEnumKeyValue` now supports `ArrayAccess` (e.g. `$item['key']`), inherited from the shared `KeyValueTrait`.

### Changed

- `@phpstan-require-extends ArrayIterator` was added to `CollectionTrait` and `FilterableCollectionTrait` to document (and let static analysis surface) the existing requirement that these traits can only be used by classes extending `ArrayIterator`.
- `KeyValue` and `EnumKeyValue\AbstractEnumKeyValue` now implement the new `KeyValueInterface` (previously each declared the underlying `ArrayAccess`/`Countable`/`IteratorAggregate`/`FromArray`/`FromIterable`/`ToArray` interfaces individually). All existing `instanceof` checks against those interfaces continue to hold.
- Documentation updated for the new methods.

### Backwards compatibility

- **Extending `AbstractCollection`/`AbstractFilterableCollection`** the upgrade is drop-in
  - The new methods (`intersect`, `merge`, `collectionOrNull`) are inherited automatically
  - No action needed unless your subclass already declares a method with one of those names
    - **Incompatible signature** ⇒ fatal `Declaration ... must be compatible with ...` error at class load.
      - Rename your method or align its signature with the interface.
    - **Compatible signature** ⇒ no error, but your implementation silently overrides the library's.
      - Verify that is intended (and that nothing relied on the new built-in behavior).
- **Direct implementers of `Collection`** (not extending the abstract classes/not using `CollectionTrait`) must add the three new methods to satisfy the interface.
- **Exception type:**
  - `diff`, `intersect`, and `merge` now throw `Exception\NotSameCollectionTypeException` instead of a plain `InvalidArgumentException` when the other collection is not the same type
  - It extends `InvalidArgumentException`, so `catch (InvalidArgumentException)` keeps working
    - Update any assertions or checks that compare the exact exception class.
- **`AbstractEnumKeyValue` now also implements `ArrayAccess`.** This is additive — no action needed. (Only relevant if a subclass already declared its own `offset*` methods, which would now override the inherited ones.)

### Other

- Introduce `CHANGELOG.md`
- Update CI components and dev dependencies
- Added a `suggest` entry for `phpunit/phpunit` (only needed by consumers extending `AbstractCollectionTestCase`).
- `composer test`/`composer test-coverage` now run with `zend.assertions=1`, matching CI.

## [7.0.0] - 2026-02-12

### Added

- `Convertible\FromStringable` interface — create an item from a `string` or `Stringable`. (#60)

### Changed

- Reworked `FilterableCollectionTrait` and expanded the filtering/grouping internals (`CollectionFilters`, `CompositeFilter`, `FilterItemTrait`).

### Backwards compatibility
- **BREAKING:** Raised the minimum supported PHP version to `>=8.4`

## [6.6.0] - 2026-01-07

### Added

- `EnumKeyTrait`. (#58)

## [6.5.0] - 2025-12-18

### Added

- `EnumKeyValue\AbstractEnumKeyValue`.

### Changed

- Replaced `kununu/scripts` with `kununu/code-tools`.

## [6.4.0] - 2025-08-12

### Added

- `FilterableCollection::filterWith()`
  - filter a collection with an anonymous function.

## [6.3.0] - 2025-08-05

### Added

- `AbstractCollectionTestCase` to help test collections downstream.

## [6.2.0] - 2025-07-03

### Added

- `Collection::hasMultipleItems()`.

## [6.1.0] - 2025-06-16

### Added

- `KeyValue`.

## [6.0.0] - 2025-04-23

### Backwards compatibility

- **BREAKING:** Raised the minimum supported PHP version to `>=8.3`.

## [5.5.0] - 2025-07-03

### Added

- Backport of `Collection::hasMultipleItems()` to the 5.x line.

## [5.4.0] - 2025-06-16

### Added

- Backport of `KeyValue` to the 5.x line.

## [5.3.0] - 2025-04-23

### Changed

- Backport from the 6.0 line to 5.x.

## [5.2.1] - 2025-03-31

### Fixed

- JMS serializer config filename.

## [5.2.0] - 2025-03-28

### Added

- New builders for `AbstractItem`.

## [5.1.0] - 2024-11-18

### Added

- `Collection::chunk()` and `Collection::eachChunk()`.

## [5.0.0] - 2024-07-15

### Added

- **BREAKING:** Introduced the collection interfaces (`Collection`, `FilterableCollection`).

## [4.1.0] - 2024-06-19

### Changed

- Split the non-builder code of `AbstractItem` into `AbstractBasicItem`.

## [4.0.0] - 2024-03-13

### Changed

- **BREAKING:** Dropped PHP 8.0 support; minimum version is now PHP 8.1.

## [3.1.1] - 2024-01-05

### Changed

- Updated continuous integration components.

## [3.1.0] - 2023-10-11

### Changed

- Improved `AbstractItem` default builders.

## [3.0.2] - 2023-06-20

### Changed

- `Convertible\From*` interfaces now also return `static`.

## [3.0.1] - 2023-03-14

### Fixed

- Addressed Sonar issues.

## [3.0.0] - 2023-03-14

### Changed

- Open sourced the library.

## [2.0.0] - 2023-03-14

### Changed

- **BREAKING:** Dropped support for PHP 7.x.

## [1.11.1] - 2024-11-18

### Added

- Backport of `chunk` / `eachChunk` to the 1.11.x line.

## [1.11.0] - 2022-12-19

### Added

- `Mapper`.

## [1.10.0] - 2022-08-26

### Added

- Additional build getters for `AbstractItem`.

## [1.9.0] - 2022-02-23

### Added

- `AutoSortableOffsetSetTrait`.

## [1.8.0] - 2022-01-19

### Fixed

- `AbstractItemToArray::toArray`.

## [1.7.0] - 2021-12-13

### Added

- `AbstractItemToArray`.

## [1.6.3] - 2021-10-06

### Changed

- Allow installation on PHP 8.

## [1.6.2] - 2021-08-26

### Changed

- Always use the latest version of `kununu/scripts`.

## [1.6.1] - 2021-05-11

### Changed

- Updated `kununu/scripts` to `^2.0`.

## [1.6.0] - 2021-03-23

### Added

- `Collection::map()` and `Collection::reduce()`.

## [1.5.0] - 2021-03-16

### Added

- Getter builders in `AbstractItem` for `string` / `bool` / `int` types.

## [1.4.0] - 2021-02-18

### Added

- `Collection::each()` to iterate through each item of the collection.

## [1.3.0] - 2021-02-05

### Added

- `AbstractItem`.

## [1.2.0] - 2021-02-01

### Changed

- Reworked the approach to the abstract collections (regular and filterable).

## [1.1.1] - 2021-02-01

### Fixed

- `CollectionTrait::fromIterable` so it can be used by `AbstractCollection` and `AbstractFilterableCollection`.

## [1.1.0] - 2021-01-29

### Added

- Abstract base collections (regular and filterable).

## [1.0.0] - 2020-08-17

### Added

- Initial release: `CollectionTrait` and `FilterableCollectionTrait`.

[unreleased]: https://github.com/kununu/collections/compare/v8.0.0...HEAD
[8.0.0]: https://github.com/kununu/collections/compare/v7.0.0...v8.0.0
[7.0.0]: https://github.com/kununu/collections/compare/v6.6.0...v7.0.0
[6.6.0]: https://github.com/kununu/collections/compare/v6.5.0...v6.6.0
[6.5.0]: https://github.com/kununu/collections/compare/v6.4.0...v6.5.0
[6.4.0]: https://github.com/kununu/collections/compare/v6.3.0...v6.4.0
[6.3.0]: https://github.com/kununu/collections/compare/v6.2.0...v6.3.0
[6.2.0]: https://github.com/kununu/collections/compare/v6.1.0...v6.2.0
[6.1.0]: https://github.com/kununu/collections/compare/v6.0.0...v6.1.0
[6.0.0]: https://github.com/kununu/collections/compare/v5.2.1...v6.0.0
[5.5.0]: https://github.com/kununu/collections/compare/v5.4.0...v5.5.0
[5.4.0]: https://github.com/kununu/collections/compare/v5.3.0...v5.4.0
[5.3.0]: https://github.com/kununu/collections/compare/v5.2.1...v5.3.0
[5.2.1]: https://github.com/kununu/collections/compare/v5.2.0...v5.2.1
[5.2.0]: https://github.com/kununu/collections/compare/v5.1.0...v5.2.0
[5.1.0]: https://github.com/kununu/collections/compare/v5.0.0...v5.1.0
[5.0.0]: https://github.com/kununu/collections/compare/v4.1.0...v5.0.0
[4.1.0]: https://github.com/kununu/collections/compare/v4.0.0...v4.1.0
[4.0.0]: https://github.com/kununu/collections/compare/v3.1.1...v4.0.0
[3.1.1]: https://github.com/kununu/collections/compare/v3.1.0...v3.1.1
[3.1.0]: https://github.com/kununu/collections/compare/v3.0.2...v3.1.0
[3.0.2]: https://github.com/kununu/collections/compare/v3.0.1...v3.0.2
[3.0.1]: https://github.com/kununu/collections/compare/v3.0.0...v3.0.1
[3.0.0]: https://github.com/kununu/collections/compare/v2.0.0...v3.0.0
[2.0.0]: https://github.com/kununu/collections/compare/v1.11.0...v2.0.0
[1.11.1]: https://github.com/kununu/collections/compare/v1.11.0...v1.11.1
[1.11.0]: https://github.com/kununu/collections/compare/v1.10.0...v1.11.0
[1.10.0]: https://github.com/kununu/collections/compare/v1.9.0...v1.10.0
[1.9.0]: https://github.com/kununu/collections/compare/v1.8.0...v1.9.0
[1.8.0]: https://github.com/kununu/collections/compare/v1.7.0...v1.8.0
[1.7.0]: https://github.com/kununu/collections/compare/v1.6.3...v1.7.0
[1.6.3]: https://github.com/kununu/collections/compare/v1.6.2...v1.6.3
[1.6.2]: https://github.com/kununu/collections/compare/v1.6.1...v1.6.2
[1.6.1]: https://github.com/kununu/collections/compare/v1.6.0...v1.6.1
[1.6.0]: https://github.com/kununu/collections/compare/v1.5.0...v1.6.0
[1.5.0]: https://github.com/kununu/collections/compare/v1.4.0...v1.5.0
[1.4.0]: https://github.com/kununu/collections/compare/v1.3.0...v1.4.0
[1.3.0]: https://github.com/kununu/collections/compare/v1.2.0...v1.3.0
[1.2.0]: https://github.com/kununu/collections/compare/v1.1.1...v1.2.0
[1.1.1]: https://github.com/kununu/collections/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/kununu/collections/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/kununu/collections/releases/tag/v1.0.0
