# CollectionTrait

This is the most basic trait, and it provides the following methods to your class:

## fromIterable
```php
public static function fromIterable(iterable $data): self|static;
```

This method tries to create an instance of your collection class with data from a source that is an `iterable` (e.g. an array).

Internally it is iterating the items in the data source and calling the `append` method of your class (or the `ArrayIterator` one if you don't rewrite it in your class).

With a concrete implementation on your class of the `append` method you can define on how to transform each iterable element into an instance of a valid object your collection will accept and handle.

## empty
```php
public function empty(): bool;
```

Just a shortcut to see if your collection has a count of elements greater than zero.

## add

```php
public function add($value): self|static;
```

A fluent version of `append`. To do stuff like:

```php
$collection->add($item1)->add($item2);
```

## unique

```php
public function unique(): self|static;
```

This method will produce a collection with distinct elements of your collection.

## reverse

```php
public function reverse(): self|static;
```

This method will produce a collection with elements of your collection in the reverse order.

## diff

```php
public function diff(self $other): self|static;
```

This method will produce a collection with the difference between your collection and another instance.

## each

```php
public function each(callable $function, bool $rewind = true): self|static;
```

This method will iterate through each item of a collection, optionally rewind it at the end of the iteration, calling an anonymous function where you can do whatever you need with each item.

Callable signature:

```php
function(mixed $element, string|float|int|bool|null $elementKey): void;
```

## map

```php
public function map(callable $function, bool $rewind = true): array;
```

This method will map your collection to an array, optionally rewind it at the end of the iteration, calling an anonymous function where you can do whatever you need with each item.

Callable signature:

```php
function(mixed $element, string|float|int|bool|null $elementKey): mixed;
```

## reduce

```php
public function reduce(callable $function, mixed $initial = null, bool $rewind = true): mixed;
```

This method will reduce your collection to a single value, optionally rewind it at the end of the iteration, calling an anonymous function where you can do whatever you need with each item.

Callable signature:

```php
function(mixed $carry, mixed $element, string|float|int|bool|null $elementKey): mixed;
```

## toArray

```php
public function toArray(): array;
```

This method will convert your collection to a representation of it as an array.

To take full use of this method also take a look at the `Kununu\Collection\Convertible` interfaces.

If the members of your collection properly implement the `ToArray`, `ToInt` and `ToString` interfaces, this method will then recursively convert each field to a basic PHP representation.
This applies also to collections that are defined inside a class that is an item in a top level collection.

Example:

```php
<?php
declare(strict_types=1);

final class MyTopCollection implements ToArray
{
    use CollectionTrait;    
}

final class MySubCollection implements ToArray
{
    use CollectionTrait;
}

final class MyTopItem implements ToArray
{
    public string $name;
    public MySubCollection $subCollection;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'subCollection' => $this->subCollection->toArray()
        ];           
    }
}
``` 
