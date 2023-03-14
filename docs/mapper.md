# Mapper

In order to allow you to do custom mapping to generate an array from your collection you could create a class that implements the following interface:

## Mapper

```php
interface Mapper
{
    public function map(AbstractCollection $collection): array;
}
```

The `map` method should receive any implementation of the `AbstractCollection` and return a array.


## DefaultMapper

To reduce code you can create your mapper classes by extending the `DefaultMapper`.

```php
public function __construct(string ...$collectionClasses);

abstract protected function getCallers(string $collectionClass): ?MapperCallers;
```

The constructor should receive the full qualified name of your collection classes.

The `getCallers` method should be implemented. It will create a `MapperCallers` instance or null if the collection class is unknown in the context of your mapper.

The `MapperCallers` should receive two closures on the constructor.

When calling the mapper constructor make sure your `getCallers` returns for the collection classes you pass to the constructor, otherwise it will throw an exception.

Also, when calling the `map` method also make sure your collection instance is of one the classes you register in the constructor, otherwise it will throw an exception.

```php
public function __construct(private Closure $fnGetId, private Closure $fnGetValue);
```

The `$fnGetId` closure should receive an instance of your collection item and return the identifier to use in the map result.

The `$fnGetValue` closure should receive an instance of your collection item and return the value to use in the map result.

### Example

```php
use Kununu\Collection\AbstractCollection;
use Kununu\Collection\Mapper\DefaultMapper;
use Kununu\Collection\Mapper\MapperCallers;

final class MyCollectionItem
{
    public $key;
    public $value;

    public function __construct(int $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }
}

final class MyCollection extends AbstractCollection
{
}

final class MyMapper extends DefaultMapper
{
    protected function getCallers(string $collectionClass): ?MapperCallers
    {
        if (MyCollection::class === $collectionClass) {
            return new MapperCallers(
                fn(MyCollectionItem $item): int => $item->key, 
                fn(MyCollectionItem $item): string => $item->value()
            );
        }

        return null;
    }    
}

$mapper = new MyMapper(MyCollection::class);

$map = $mapper->map(
    new MyCollection(
        new MyCollectionItem(1, 'Item 1'),
        new MyCollectionItem(2, 'Item 2')
    )
);
/*
Value of $map:
[
    1 => 'Item 1',
    2 => 'Item 2'
]
*/

$mapper->map(new AnotheCollection());
// Will throw exception because mapper could not create a `MapperCallers` instance
// for this collection class


$mapper = new MyMapper(AnotheCollection::class);
// Will throw exception because mapper could not create a `MapperCallers` instance
// for this collection class
```
