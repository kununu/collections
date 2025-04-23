# Abstract Collections

## AbstractCollection

This is an abstract base class that you can use for your collections. It extends `ArrayIterator` and implements the `Collection` interface (and already uses the `CollectionTrait`) so you just need to extend it to have a proper collection class.

```php
<?php
declare(strict_types=1);

use Kununu\Collection\AbstractCollection;

/**
 * @method static self fromIterable(iterable $data)
 * @method        self add(mixed $value)
 * @method        self clear()
 * @method        self diff(Collection $other)
 * @method        self duplicates(bool $strict = true, bool $uniques = false)
 * @method        self each(callable $function, bool $rewind = true)
 * @method        self reverse()
 * @method        self unique()
 */
final class MyCollection extends AbstractCollection 
{
}

$collection = MyCollection::fromIterable($myData);
```

The docblocks are advisable to help your IDE recognize the proper collection items types.

## AbstractFilterableCollection

Using the same concept as `AbstractCollection` this class extends `ArrayIterator` and implements the `FilterableCollection` (and already uses the `FilterableCollectionTrait`).

```php
<?php
declare(strict_types=1);

use Kununu\Collection\AbstractFilterableCollection;

/**
 * @method static self fromIterable(iterable $data)
 * @method        self add(mixed $value)
 * @method        self clear()
 * @method        self diff(Collection $other)
 * @method        self duplicates(bool $strict = true, bool $uniques = false)
 * @method        self each(callable $function, bool $rewind = true)
 * @method        self reverse()
 * @method        self unique()
 * @method        self filter(CollectionFilter $filter)
 */
final class MyCollection extends AbstractFilterableCollection
{
}

$collection = MyCollection::fromIterable($myData);

$filtered = $collection->filter($filter);
$groups = $collection->groupBy(true, $group1Filter, $group2Filter);
```
The docblocks are advisable to help your IDE recognize the proper collection items types.
