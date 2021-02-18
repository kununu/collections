# Collections

The goal of this library is to provide some boilerplate code to assist you in creating more friendly collections when using `ArrayIterator`.

## Install

#### Add custom private repository to composer.json

```json
...
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/kununu/collections.git",
        "no-api": true
    }
],
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

The library provide two traits that you can add to your custom class extending `ArrayIterator`.

It defines interfaces to convert collection items to `array`, `string` and `int` and to compare items.

It also provides some interfaces to filter and group data on your collections and base classes with default implementations.

### CollectionTrait

This is the most basic trait and it provides the following methods to your class:

#### fromIterable
```php
public static function fromIterable(iterable $data): self;
```

This method tries to create an instance of your collection class with data from a source that is an `iterable` (e.g. an array).

Internally it is iterating the items in the data source and calling the `append` method of your class (or the `ArrayIterator` one if you don't rewrite it in your class).

With a concrete implementation on your class of the `append` method you can define on how to transform each iterable element into an instance of a valid object your collection will accept and handle.

#### empty
```php
public function empty(): bool;
```

Just a shortcut to see if you collection has a count of elements greater than zero.

#### add

```php
public function add($value): self;
```

A fluent version of `append`. To do stuff like:

```php
$collection->add($item1)->add($item2);
```

#### unique

```php
public function unique(): self;
```

This method will produce a collection with distinct elements of your collection.

#### reverse

```php
public function reverse(): self;
```

This method will produce a collection with elements of your collection in the reverse order. 

#### diff

```php
public function diff(self $other): self;
```

This method will produce a collection with the difference between your collection and another instance.

#### each

```php
public function each(callable $function, bool $rewind = true): self;
```

This method will iterate through each item of a collection, optionally rewind it at the end of the iteration, calling an anonymous function where you can do whatever you need with each item. 

#### toArray 

```php
public function toArray(): array;
```

This method will convert your collection to a representation of it as an array.

To take full use of this method also take a look at the `Kununu\Collection\Convertible` interfaces.

If the members of your collection properly implement the `ToArray`, `ToInt` and `ToString` interfaces, this method will then recursively convert each field to a basic PHP representation.
This applies also to collections that are defined inside a class that is an item in a top level collection.


Example:

```php
class MyTopCollection implements ToArray
{
    use CollectionTrait;    
}

class MySubCollection implements ToArray
{
    use CollectionTrait;
}

class MyTopItem implements ToArray
{
    public $name;
    public $subCollection;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'subCollection' => $this->subCollection->toArray()
        ];           
    }
}
``` 

### Convertible

The following interfaces are defined to easy the process of recursively your collection data to basic PHP items: 

#### toArray

This interface defines how to convert a collection item (or a collection itself) to an array:

```php
interface ToArray
{
    public function toArray(): array;
}
```

#### toInt

This interface defines how to convert a collection item to an integer:

```php
interface ToInt
{
    public function toInt(): int;
}
```

#### toString

This interface defines how to convert a collection item to a string:

```php
interface ToString
{
    public function toString(): string;
}
```

### FilterableCollectionTrait

This trait (which internally also uses the `CollectionTrait`) adds filtering capabilities to your collection. 

It provides the following methods to your collection:

#### filter

```php
public function filter(CollectionFilter $filter): self
```

This will accept a `CollectionFilter` instance (with the definitions of the filter being applied to the collection), and returns a new collection with only the elements that have met the criteria defined in `$filter`.

#### groupBy

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

### How to filter collections

#### FilterItem

```php
interface FilterItem
{
    public function groupByKey(?array $customData = null): string;
}
```

In order to the use the `FilterableCollectionTrait`, the items on your collection must implement the `FilteItem` interface.

Each item needs to implement the `groupByKey` method that will be used to group items. This method return the key of the group and will receive additional extra data that might be necessary in order to group the items.

#### CollectionFilter

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

#### CompositeFilter

The `CompositeFilter` is an implementation of a `CollectionFilter` that allows to create composite filters. This mean that it can have conditions with multiple filters inside.

Each composite filter will receive a `FilterOperator` and the respective sub-filters.

To implement multi-level operation we can use a `CompositeFilter` inside another `CompositeFilter` (see example on filtering collections bellow).

##### FilterOperator

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

By default the library provides two implementations of this interface: `FilterOperatorAnd` and `FilterOperatorOr`.

A quick example:
```php
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
        // Filter1 AND Filter3 AND (Filter2 OR Filter4) 
        new FilterOperatorAnd(),
        new MyFilter1(),
        new MyFilter3(),
        new CompositeFilter(
            new FilterOperatorOr(),
            new MyFilter2(),
            new MyFilter4()
        )
   )
);
```
### AbstractCollection

This is an abstract base class that you can use for your collections. It extends `ArrayIterator` (and already uses the `CollectionTrait`) so you just need to extend it to have a proper collection class.

```php
use Kununu\Collection\AbstractCollection;

class MyCollection extends AbstractCollection 
{
}

$collection = MyCollection::fromIterable($myData);
```
### AbstractFilterableCollection

Using the same concept as `AbstractCollection` this class extends `ArrayIterator` and add the `FilterableCollectionTrait` to it.

```php
use Kununu\Collection\AbstractFilterableCollection;

class MyCollection extends AbstractFilterableCollection
{
}

$collection = MyCollection::fromIterable($myData);

$filtered = $collection->filter($filter);
$groups = $collection->groupBy(true, $group1Filter, $group2Filter);

```

### AbstractItem

This class provide an abstract "item" that you can add to your collections.
Instead of writing and defining all the properties and respective getters/setters of your objects this class provides an easy way to achieve that by using PHP's `__call` method to magically provide those getters and setters.

Since many of the times we are building those objects based on data collected from some source (usually getting it in an array) this abstract class also provides a way to "build" your instances based on array data.

The basic structure of a class extending this is the following:

```php
class MyItem extends AbstractItem
{
    protected const PROPERTIES = [
        'id',
        'name',
        'createdAt'
    ];

    public function __construct()
    {
        parent::__construct(['createdAt' => new DateTime()]);
    }

    protected static function getBuilders(): array
    {
        return [
            'id'        => function(array $data) {
                return $data['id'] ?? null;
            },
            'name'      => function(array $data) {
                return $data['name'] ?? null;
            },
            'createdAt' => function(array $data) {
                return $data['createdAt'] ?? null;
            },
        ];
    }
}

```

#### Defining the properties

Looking at the snippet above one must define which properties our class will hold. So override the constanc `PROPERTIES` and these will be the properties available.

##### Naming conventions for setters and getters

Based on the properties defined on your subclass the class will accept calls to each of them with the format:

Field: `myField`

Setter: `$item->setMyField($value);`

Getter: `$value = $item->getMyField()`

If you want to use different prefixes for setters/getters you can! Just override the `SETTER_PREFIX` and/or `GETTER_PREFIX` constants.

Example:

```php
class MyItem extends AbstractItem
{
    protected const PROPERTIES = [
        'id',
        'name',
        'createdAt'
    ];

    protected const SETTER_PREFIX = 'change';
    protected const GETTER_PREFIX = '';
}
```

Field: `myField`

Setter: `$item->changeMyField($value);`

Getter: `$value = $item->myField()`

#### Initialization

By default the abstract class can receive an array that will be used to populate the properties. If we want we can override the constructor and pass some of the default values to the parent.

#### Code hints

Since all the setters/getters are "magic" we might feel the need to have code hints. Just declare PHPDoc blocks in your class.

Example:

```php
/**
 * @method null|int getId()
 * @method null|string getName()
 * @method null|DateTime getCreatedAt()
 * @method null|string getExtraFieldNotUsedInBuild()
 * @method MyItem setId(?int $id)
 * @method MyItem setName(?string $name)
 * @method MyItem setCreatedAt(?DateTime $createdAt)
 */
class MyItem extends AbstractItem
{
    protected const PROPERTIES = [
        'id',
        'name',
        'createdAt'
    ];
}
```

#### Builders

As said above the class can help you building your instances from data stored in arrays. Every subclass will have the method `build`.


```php
    /**
     * @param array $data
     *
     * @return static
     */
    public static function build(array $data): self;
```


But in order for it to work your subclass must override the `getBuilders` static method.

This method will basically return a map of property => callable to get data for the property:

```php
[
    'itemProperty' => function(array $data) { return $valueForTheProperty; }
]
```
