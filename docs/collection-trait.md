# CollectionTrait

This is the most basic trait, and it provides an implementation of the `Collection` interface.

Some details about the implementation:

## fromIterable

Internally it is iterating the items in the data source and calling the `append` method of your class (or the `ArrayIterator` one if you don't rewrite it in your class).

With a concrete implementation on your class of the `append` method you can define on how to transform each iterable element into an instance of a valid object your collection will accept and handle.

## toArray

```php
public function toArray(): array;
```

This method will convert your collection to a representation of it as an array.

To take full use of this method also take a look at the [Convertible](../src/Convertible) interfaces.

If the members of your collection properly implement the `ToArray`, `ToInt` and `ToString` interfaces, this method will then recursively convert each field to a basic PHP representation.

Also, if any element is an implementation of [Stringable](https://www.php.net/manual/en/class.stringable.php), it will convert the element to string by casting it to string (in practice it will call the `__toString` method that `Stringable` enforces).

This applies also to collections that are defined inside a class that is an item in a top level collection.

Example:

```php
<?php
declare(strict_types=1);

use Kununu\Collection\AbstractCollection;

final class MyTopCollection extends AbstractCollection
{
}

final class MySubCollection extends AbstractCollection
{
}

final class MySubItem implements Stringable
{
    public function __construct(public readonly int $age)
    {    
    }
    
    public function __toString(): string
    {
        return (string) $this->age;     
    }
}

final class MyTopItem implements ToArray
{
    public function __construct(public string $name, public MySubCollection $subCollection)
    {    
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'subCollection' => $this->subCollection->toArray(),
        ];           
    }
}

$collection = new MyTopCollection();

$collection->add(
    new MyTopItem(
        'The Name',
        (new MySubCollection())->add(new MySubItem(100))
    )
);

$collection->toArray();

// Will result in:
[
    [
        'name' => 'The Name',
        'subCollection' => [
            '100'
        ]
    ]   
];
``` 

## add

Internally it is call the `ArrayIterator::append` and returning the instance to allow fluent calls.

## chunk

Internally this method chunks [a copy](https://www.php.net/manual/arrayobject.getarraycopy.php) of the collection with the [`array_chunk` php function](https://www.php.net/manual/function.array-chunk.php), returning a zero indexed array of collections of the same type as the initial one.

## diff

To check the difference between two collections first it checks that the other collection is of the same type as the current one.

Then it is calling the PHP [array_diff](https://www.php.net/manual/en/function.array-diff.php) between the [serialize](https://www.php.net/manual/en/https://www.php.net/manual/en/function.serialize.php) representation of each collection represented as an array (by calling the `toArray` method on each collection).

Finally, it is creating a new instance of the collection by using [unserialize](https://www.php.net/manual/en/function.unserialize) on each member of the diff.

## duplicates

Internally it creates two new collections. One for the non-duplicated elements. Another one for the duplicates.

Internally it is iterating the collection with the `each` method and for each element checks if it is already in the non-duplicated elements collection (by using the `has` method).
    - If it's already there the element will be added to the duplicated collection
    - Otherwise it will be added to the non-duplicated collection

Finally, it will return the duplicated collection, optionally calling the `unique` method if we don't want the same duplicated elements in the result.

## each

Internally, this method will iterate through each item of a collection, optionally rewind it at the end of the iteration, calling the anonymous function for each element.

## eachChunk

Internally, this method calls the [chunk](#chunk) function and then executes the passed anonymous function with each chunk.

## empty

Internally is checking if the `ArrayIterator::count` returns 0

## has

Internally, this method is calling the PHP [in_array](https://www.php.net/manual/en/function.in-array.php) to check if the element is in the array representation of the collection (obtained via `toArray` method).

Please note that since it's using `in_array` it can produce unexpected results when using loose checking.

## keys

Internally, this method is calling the PHP [array_keys](https://www.php.net/manual/en/function.array-keys) of the array held in the `ArrayIterator` (via the `ArrayIterator::getArrayCopy` method)

## map

Internally, this method will iterate through your collection, optionally rewind it at the end of the iteration, calling the anonymous function and storing the result of that call in an array which is the result.

## reduce

Internally, this method will reduce your collection to a single value, optionally rewind it at the end of the iteration, calling the anonymous function for each element and updating the `$initial` value with the result of the call.

Finally, it will return the updated `$initial` value as the result of the reduction.

## reverse

Internally, this method will call the PHP [array_reverse](https://www.php.net/manual/en/function.array-reverse) on the array representation of the collection (obtained via `toArray` method).

Then it will create and return a new instance of the collection (with `toIterable`) with the result of the `array_reverse`.

## unique

Internally, this method will call the PHP [array_unique](https://www.php.net/manual/en/function.array-unique.php) on the array representation of the collection (obtained via `toArray` method).

Then it will create and return a new instance of the collection (with `toIterable`) with the result of the `array_unique`.

## values

Internally, this method is calling the PHP [array_values](https://www.php.net/manual/en/function.array-values.php) of the array held in the `ArrayIterator` (via the `ArrayIterator::getArrayCopy` method)
