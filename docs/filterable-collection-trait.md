# FilterableCollectionTrait

This trait (which internally also uses the `CollectionTrait`) adds filtering capabilities to your collection.

It provides the following methods to your collection:

## filter

```php
public function filter(CollectionFilter $filter): self|static
```

This will accept a `CollectionFilter` instance (with the definitions of the filter being applied to the collection), and returns a new collection with only the elements that have met the criteria defined in `$filter`.

## groupBy

```php
public function groupBy(bool $removeEmptyGroups, CollectionFilter ...$filters): array
```

This method allows you to apply a series of filters to a collection and group the result by each filter. The `$removeEmptyGroups` flag means if we will remove or keep groups in the result without items.

The result is returned as an array with the following structure:

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

## How to filter collections

### FilterItem

```php
interface FilterItem
{
    public function groupByKey(?array $customData = null): string;
}
```

In order to the use the `FilterableCollectionTrait`, the items on your collection must implement the `FilteItem` interface.

Each item needs to implement the `groupByKey` method that will be used to group items. This method return the key of the group and will receive additional extra data that might be necessary in order to group the items.

### CollectionFilter

To filter collections you must define classes that implement the `CollectionFilter` interface.

The library already provides a default abstract implementation in the class `BaseFilter`.

```php
interface CollectionFilter
{
    public function key(): string;

    public function isSatisfiedBy(FilterItem $item): bool;

    public function customGroupByData(): ?array;

    public function setCustomGroupByData(?array $customGroupByData = null): CollectionFilter;
}
```

Each filter must return a key with the method `key` and also the `isSatisfiedBy` method which will determine if the item satisfies the conditions of the filter.

### CompositeFilter

The `CompositeFilter` is an implementation of a `CollectionFilter` that allows to create composite filters. This mean that it can have conditions with multiple filters inside.

Each composite filter will receive a `FilterOperator` and the respective sub-filters.

To implement multi-level operation we can use a `CompositeFilter` inside another `CompositeFilter` (see example on filtering collections bellow).

#### FilterOperator

```php
interface FilterOperator
{
    public function calculate(bool $operand1, bool $operand2): bool;

    public function initialValue(): bool;

    public function exitConditionValue(): bool;
}
```

So basically it defines a boolean operation.

The `initialValue` is the value to use if you want to perform several operations (e.g., in an `AND` we will use `true`).

The `exitConditionValue` is the value to use if you want to exit the calculation when performing several calculations (like a short circuit evaluation).

By default, the library provides three implementations of this interface: `FilterOperatorAnd`, `FilterOperatorOr` and `FilterOperatorXOr`.

A quick example:
```php
<?php
declare(strict_types=1);

$filter = CompositeFilter(
    new FilterOperatorAnd(),
    new MyFilter1(),
    new MyFilter2(),
    new MyFilter3()
);
```

Then the `isSatisfiedBy` method will apply the `FilterOperator` for each filter passed to the `CompositeFilter`. In this example above it will basically perform an `AND` operation for each filter, meaning it can read it as:

```php
$myFilter1->isSatisfiedBy(...) && $myFilter2->isSatisfiedBy(...) && $myFilter3->isSatisfiedBy(...)  
```

## Example on filtering collections

So to wrap it up, on how to filter/group a collection:

```php
<?php
declare(strict_types=1);

$filteredCollection = $collection->filter(
    // Filter1 AND Filter2 AND Filter3    
    new CompositeFilter(
        new FilterOperatorAnd(),
        new MyFilter1(),
        new MyFilter2(),
        new MyFilter3()
    )
);

$groupByResults = $collection->groupBy(
    true,
    // First group
    new CompositeFilter(
        // Filter1 OR Filter 2
        new FilterOperatorOr(),
        new MyFilter1(),
        new MyFilter2()
    ),
    // Second group
    new CompositeFilter(
        // Filter1 AND Filter3 AND (Filter2 XOR Filter4) 
        new FilterOperatorAnd(),
        new MyFilter1(),
        new MyFilter3(),
        new CompositeFilter(
            new FilterOperatorXor(),
            new MyFilter2(),
            new MyFilter4()
        )
   )
);
```
