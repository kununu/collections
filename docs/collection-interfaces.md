# Collection Interfaces

The library defines two interfaces for collections.

The first one ([`Collection`](../src/Collection.php)) is a basic "bare-bones" collection.

The second one ([`FilterableCollection`](../src/FilterableCollection.php)) is a "filterable" collection, which allows you to filter and group elements on your collection.

`FilterableCollection` extends `Collection`, so all methods should also be implemented in a `FilterableCollection` (plus the specific methods).  

Every collection interface is also extending from `FromIterable` and `ToArray` interfaces. 

So they should also implement the following methods:

## fromIterable

```php
public static function fromIterable(iterable $data): self|static;
```

This method should try to create an instance of your collection class with data from a source that is an `iterable` (e.g. an array).

## toArray

```php
public function toArray(): array;
```

This method will convert your collection to a representation of it as an array.

## Collection

The `Collection` interface defines the methods your collection should provide.

### add

```php
public function add(mixed $value): self|static;
```

This method is defined to be a fluent version of `ArrayIterator::append`. To do stuff like:

```php
$collection->add($item1)->add($item2);
```

### chunk

```php
/** @return self[]|static[] */
public function chunk(int $size): array
```

This method [mirrors the behavior of `array_chunk`](https://www.php.net/manual/function.array-chunk.php) and returns a zero indexed numeric array of the current collection based on the chunk size provided.

## clear

This method should remove all the items of the collection and since is fluent it should return the collection. 

### diff

```php
public function diff(self $other): self|static;
```

This method will produce a collection with the difference between your collection and another instance.

### duplicates

```php
public function duplicates(bool $strict = true, bool $uniques = false): self|static;
```

This method produces a collection which contains items which occur multiple times in the collection.

- `$strict` parameter allows you to use strict comparison (e.g. if your implementation uses PHP `in_array`).
- `$uniques` parameter allows you to specify if you want to return unique duplicates values instead of all duplicates entries.

### each

```php
public function each(callable $function, bool $rewind = true): self|static;
```

This method will iterate through each item of a collection, optionally rewind it at the end of the iteration, calling an anonymous function where you can do whatever you need with each item.

Callable signature:

```php
function(mixed $element, string|float|int|bool|null $elementKey): void;
```

### eachChunk

```php
public function eachChunk(int $size, callable $function): self|static
```

This method chunks the collection using the passed `$size` and executes the given anonymous function with each chunk.

Callable signature:

```php
function(CollectionInterface $collection): void;
```

### empty

```php
public function empty(): bool;
```

Just a shortcut to see if your collection has a count of elements greater than zero.

### has

```php
public function has(mixed $value, bool $strict = true): bool;
```

This method will tell you if your collection contains the given value.
 
- `$strict` parameter allows you to use strict comparison (e.g. if your implementation uses PHP `in_array`).

### keys

```php
public function keys(): array;
```

This method will return the keys of the collection.

### map

```php
public function map(callable $function, bool $rewind = true): array;
```

This method will map your collection to an array, optionally rewind it at the end of the iteration, calling an anonymous function where you can do whatever you need with each item.

Callable signature:

```php
function(mixed $element, string|float|int|bool|null $elementKey): mixed;
```

### reduce

```php
public function reduce(callable $function, mixed $initial = null, bool $rewind = true): mixed;
```

This method will reduce your collection to a single value, optionally rewind it at the end of the iteration, calling an anonymous function where you can do whatever you need with each item.

Callable signature:

```php
function(mixed $carry, mixed $element, string|float|int|bool|null $elementKey): mixed;
```

### reverse

```php
public function reverse(): self|static;
```

This method will produce a collection with elements of your collection in the reverse order.

### unique

```php
public function unique(): self|static;
```

This method will produce a collection with distinct elements of your collection.

### values

```php
public function values(): array;
```

This method will return the values of the collection.

## FilterableCollection

The `FilterableCollection` interface defines additional methods that your collection should provide in order to filter and/or group elements in it.

### filter

```php
public function filter(CollectionFilter $filter): self|static
```

This will accept a `CollectionFilter` instance (with the definitions of the filter being applied to the collection), and returns a new collection with only the elements that have met the criteria defined in `$filter`.

### groupBy

```php
public function groupBy(bool $removeEmptyGroups, CollectionFilter ...$filters): array
```

This method allows you to apply a series of filters to a collection and group the result by each filter. The `$removeEmptyGroups` flag means if we will remove or keep groups in the result without items.

The result should be returned as an array with the following structure:

```php
[
    'filter_1_key' => [
        'item_key_1' => item object 1
        ...
        'item_key_N' => item object X
     ],
     'filter_2_key' => [
     'item_key_1' => item object 1
     ...
     'item_key_N' => item object Y
     ],
     ...
]
```
