# Abstract Item

## AbstractItem

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
            'simpleName' => self::buildStringGetter('simpleName'),
            'verified'   => self::buildBoolGetter('verified'),
            'industryId' => self::buildIntGetter('industryId'),
        ];
    }
}

```

### Defining the properties

Looking at the snippet above one must define which properties our class will hold. So override the constanc `PROPERTIES` and these will be the properties available.

#### Naming conventions for setters and getters

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

### Initialization

By default, the abstract class can receive an array that will be used to populate the properties. If we want we can override the constructor and pass some of the default values to the parent.

### Code hints

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

### Builders

As said above the class can help you to build your instances from data stored in arrays. Every subclass will have the method `build`.


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

#### Provided builders

##### buildStringGetter

```php
protected static function buildStringGetter(string $fieldName, ?string $default = null): callable;
```

Returns a string or `$default` value from the `$fieldName` in the data

##### buildRequiredStringGetter

```php
protected static function buildRequiredStringGetter(string $fieldName): callable;
```

Returns a string from the `$fieldName` in the data or throws an exception if no data is found.

##### buildBoolGetter

```php
protected static function buildBoolGetter(string $fieldName, ?bool $default = null): callable;
```

Returns a boolean or `$default` value from the `$fieldName` in the data

##### buildRequiredBoolGetter

```php
protected static function buildRequiredBoolGetter(string $fieldName): callable;
```

Returns a boolean from the `$fieldName` in the data or throws an exception if no data is found.

##### buildIntGetter

```php
protected static function buildIntGetter(string $fieldName, ?int $default = null): callable;
```

Returns an integer or `$default` value from the `$fieldName` in the data

##### buildRequiredIntGetter

```php
protected static function buildRequiredIntGetter(string $fieldName): callable;
```

Returns an integer from the `$fieldName` in the data or throws an exception if no data is found.

##### buildFloatGetter

```php
protected static function buildFloatGetter(string $fieldName, ?float $default = null): callable;
```

Returns a float or `$default` value from the `$fieldName` in the data

##### buildRequiredFloatGetter

```php
protected static function buildRequiredFloatGetter(string $fieldName): callable;
```

Returns a float from the `$fieldName` in the data or throws an exception if no data is found.

##### buildDateTimeGetter

```php
protected static function buildDateTimeGetter(string $fieldName, string $dateFormat = self::DATE_FORMAT, ?DateTime $default = null): callable
```

Returns a DateTime or `$default` value from the `$fieldName` in the data.

The `$dateFormat` by default is `'Y-m-d H:i:s'`.

##### buildRequiredDateTimeGetter

```php
protected static function buildRequiredDateTimeGetter(string $fieldName, string $dateFormat = self::DATE_FORMAT): callable;
```

Returns a DateTime from the `$fieldName` in the data or throws an exception if no data is found.

The `$dateFormat` by default is `'Y-m-d H:i:s'`.

##### buildGetterRequiredField

```php
protected static function buildGetterRequiredField(string $fieldName, callable $converter): callable;
```

Returns a value from the `$fieldName` in the data or throws an exception if no data is found.

The `$converter` callable should implement the logic assuming that the `$fieldName` exists in the data and it will receive the `$value` on that field.

##### buildCollectionGetter

```php
protected static function buildCollectionGetter(string $fieldName, string $collectionClass, ?AbstractCollection $default = null): callable;
```

Try to create an `AbstractCollection` concrete implementation instance which class is `$collectionClass` from the `$fieldName` in the data.

The `$fieldName` in the data must be an `iterable` suited to be passed to the static `fromIterable` method of your `$collectionClass`.

- If `$collectionClass` is not a concrete subclass of `AbstractCollection` it will return `null`.
- If `$fieldName` is not set in the data array it will return the `$default` instance (which by default is `null`).

##### buildConditionalGetter

```php
protected static function buildConditionalGetter(string $sourceField, array $sources): callable;
```

Builds a field based on the `$sourceField` value (the condition). The `$sourceField` should have the key used to select the `callable` mapped on the `$sources` array.

The `$sources` array has the format `['sourceName1' => callableForSource1(), ..., 'sourceNameN' => callableForSourceN()]` 

Example:
```php

final class MyItem extends AbstractItem
{
    protected const PROPERTIES = [
        'value',
    ];

    protected static function getBuilders(): array
    {
        return [
            'value' => self::buildConditionalGetter(
                'source',
                [
                    'one' => self::buildRequiredStringGetter('field_1'),
                    'two' => self::buildRequiredIntGetter('field_2'),
                ]
            ),
        ];
    }
}

// Output: 'Value from source 1'
var_export(MyItem::build(['source' => 'one', 'field_1' => 'Value from source 1', 'field_2' => 500])->value());

// Output: 500
var_export(MyItem::build(['source' => 'two', 'field_1' => 'Value from source 1', 'field_2' => 500])->value());

```

## AbstractItemToArray

This class provide an abstract "item" that implements the `ToArray` interface.

If the members of your class properly implement the `ToArray`, `ToInt` and `ToString` interfaces, this method will then recursively convert each field to a basic PHP representation.
