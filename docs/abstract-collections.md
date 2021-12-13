# Abstract Collections

## AbstractCollection

This is an abstract base class that you can use for your collections. It extends `ArrayIterator` (and already uses
the `CollectionTrait`) so you just need to extend it to have a proper collection class.

```php
use Kununu\Collection\AbstractCollection;

class MyCollection extends AbstractCollection 
{
}

$collection = MyCollection::fromIterable($myData);
```

## AbstractFilterableCollection

Using the same concept as `AbstractCollection` this class extends `ArrayIterator` and add
the `FilterableCollectionTrait` to it.

```php
use Kununu\Collection\AbstractFilterableCollection;

class MyCollection extends AbstractFilterableCollection
{
}

$collection = MyCollection::fromIterable($myData);

$filtered = $collection->filter($filter);
$groups = $collection->groupBy(true, $group1Filter, $group2Filter);
```
