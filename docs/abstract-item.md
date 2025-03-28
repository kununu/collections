# Abstract Item

## AbstractItem

This class provide an abstract "item" that you can add to your collections.
Instead of writing and defining all the properties and respective getters/setters of your objects this class provides an easy way to achieve that by using PHP's `__call` method to magically provide those getters and setters.

Since many of the times we are building those objects based on data collected from some source (usually getting it in an array) this abstract class also provides a way to "build" your instances based on array data.

The basic structure of a class extending this is the following:

```php
<?php
declare(strict_types=1);

final class MyItem extends AbstractItem
{
    protected const PROPERTIES = [
        'id',
        'name',
        'createdAt'
        'simpleName',
        'verified',
        'industryId',
    ];

    public function __construct()
    {
        parent::__construct(['createdAt' => new DateTime()]);
    }

    protected static function getBuilders(): array
    {
        return [
            'id'        => static fn(array $data): ?int => $data['id'] ?? null,
            'name'      => static fn(array $data): ?string => $data['name'] ?? null,
            'createdAt' => static fn(array $data)? DateTime => $data['createdAt'] ?? null,
            'simpleName' => self::buildStringGetter('simpleName'),
            'verified'   => self::buildBoolGetter('verified'),
            'industryId' => self::buildIntGetter('industryId'),
        ];
    }
}
```

### Defining the properties

Looking at the snippet above one must define which properties our class will hold. So override the constant `PROPERTIES` and these will be the properties available.

#### Naming conventions for setters and getters

Based on the properties defined on your subclass the class will accept calls to each of them with the format:

Field: `myField`

Setter: `$item->setMyField($value);`

Getter: `$value = $item->getMyField()`

If you want to use different prefixes for setters/getters you can! Just override the `SETTER_PREFIX` and/or `GETTER_PREFIX` constants.

Example:

```php
<?php
declare(strict_types=1);

final class MyItem extends AbstractItem
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
<?php
declare(strict_types=1);

/**
 * @method int|null getId()
 * @method string|null getName()
 * @method DateTime|null getCreatedAt()
 * @method string|null getExtraFieldNotUsedInBuild()
 * @method MyItem setId(?int $id)
 * @method MyItem setName(?string $name)
 * @method MyItem setCreatedAt(?DateTime $createdAt)
 */
final class MyItem extends AbstractItem
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
<?php
declare(strict_types=1);

/**
 * @param array $data
 *
 * @return static
 */
public static function build(array $data): self;
```

But in order for it to work your subclass must implement the `getBuilders` static method.

This method will basically return a map of `'property'` => `callable` to get data for the property:

```php
[
    'itemProperty' => static fn(array $data): mixed => $valueForTheProperty,
]
```

#### Provided builders

##### buildStringGetter

```php
protected static function buildStringGetter(string $fieldName, ?string $default = null, bool $useSnakeCase = false): callable;
```

Returns a `string` or `$default` value from the `$fieldName` in the data.

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array. 

##### buildRequiredStringGetter

```php
protected static function buildRequiredStringGetter(string $fieldName, bool $useSnakeCase = false): callable;
```

Returns a `string` from the `$fieldName` in the data or throws an exception if no data is found.

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildBoolGetter

```php
protected static function buildBoolGetter(string $fieldName, ?bool $default = null,bool $useSnakeCase = false): callable;
```

Returns a `bool` or `$default` value from the `$fieldName` in the data

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildRequiredBoolGetter

```php
protected static function buildRequiredBoolGetter(string $fieldName, bool $useSnakeCase = false): callable;
```

Returns a `bool` from the `$fieldName` in the data or throws an exception if no data is found.

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildIntGetter

```php
protected static function buildIntGetter(string $fieldName, ?int $default = null, bool $useSnakeCase = false): callable;
```

Returns an `int` or `$default` value from the `$fieldName` in the data

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildRequiredIntGetter

```php
protected static function buildRequiredIntGetter(string $fieldName, bool $useSnakeCase = false): callable;
```

Returns an `int` from the `$fieldName` in the data or throws an exception if no data is found.

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildFloatGetter

```php
protected static function buildFloatGetter(string $fieldName, ?float $default = null, bool $useSnakeCase = false): callable;
```

Returns a `float` or `$default` value from the `$fieldName` in the data

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildRequiredFloatGetter

```php
protected static function buildRequiredFloatGetter(string $fieldName, bool $useSnakeCase = false): callable;
```

Returns a `float` from the `$fieldName` in the data or throws an exception if no data is found.

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildDateTimeGetter

```php
protected static function buildDateTimeGetter(string $fieldName, string $dateFormat = AbstractItem::DATE_FORMAT, ?DateTimeInterface $default = null, bool $useSnakeCase = false): callable;
```

Returns a `DateTime` or `$default` value from the `$fieldName` in the data.

The `$dateFormat` by default is `'Y-m-d H:i:s'`.

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildRequiredDateTimeGetter

```php
protected static function buildRequiredDateTimeGetter(string $fieldName, string $dateFormat = AbstractItem::DATE_FORMAT, bool $useSnakeCase = false): callable;
```

Returns a `DateTime` from the `$fieldName` in the data or throws an exception if no data is found.

The `$dateFormat` by default is `'Y-m-d H:i:s'`.

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildDateTimeImmutableGetter

```php
protected static function buildDateTimeImmutableGetter(string $fieldName, string $dateFormat = AbstractItem::DATE_FORMAT, ?DateTimeInterface $default = null, bool $useSnakeCase = false): callable;
```

Returns a `DateTimeImmutable` or `$default` value from the `$fieldName` in the data.

The `$dateFormat` by default is `'Y-m-d H:i:s'`.

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildRequiredDateTimeImmutableGetter

```php
protected static function buildRequiredDateTimeImmutableGetter(string $fieldName, string $dateFormat = AbstractItem::DATE_FORMAT, bool $useSnakeCase = false): callable;
```

Returns a `DateTimeImmutable` from the `$fieldName` in the data or throws an exception if no data is found.

The `$dateFormat` by default is `'Y-m-d H:i:s'`.

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildGetterOptionalField

```php
protected static function buildGetterOptionalField(string $fieldName, callable $converter, mixed $default = null, bool $useSnakeCase = false): callable;
```

Returns a value or `$default` value from the `$fieldName` in the data.

The `$converter` callable should implement the logic assuming that the `$fieldName` exists in the data, and it will receive the `$value` on that field.

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildGetterRequiredField

```php
protected static function buildGetterRequiredField(string $fieldName, callable $converter): callable;
```

Returns a value from the `$fieldName` in the data or throws an exception if no data is found.

The `$converter` callable should implement the logic assuming that the `$fieldName` exists in the data, and it will receive the `$value` on that field.

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildFromArrayGetter

```php
protected static function buildFromArrayGetter(string $fieldName, string $fromArrayClass, ?FromArray $default = null, bool $useSnakeCase = false): callable; 
```

Try to create an `FromArray` concrete implementation instance which class is `$fromArrayClass` from the `$fieldName` in the data.

The `$fieldName` in the data must be an `array` suited to be passed to the static `fromArray` method of your `$collectionClass`.

- If `$fromArrayClass` is not a concrete subclass of `FromArray` it will return `null`.
- If `$fieldName` is not set in the data array it will return the `$default` instance (which by default is `null`).

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildCollectionGetter

```php
protected static function buildCollectionGetter(string $fieldName, string $collectionClass, ?AbstractCollection $default = null, bool $useSnakeCase = false): callable;
```

Try to create an `AbstractCollection` concrete implementation instance which class is `$collectionClass` from the `$fieldName` in the data.

The `$fieldName` in the data must be an `iterable` suited to be passed to the static `fromIterable` method of your `$collectionClass`.

- If `$collectionClass` is not a concrete subclass of `AbstractCollection` it will return `null`.
- If `$fieldName` is not set in the data array it will return the `$default` instance (which by default is `null`).

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildConditionalGetter

```php
protected static function buildConditionalGetter(string $sourceField, array $sources, bool $useSnakeCase = false): callable;
```

Builds a field based on the `$sourceField` value (the condition). The `$sourceField` should have the key used to select the `callable` mapped on the `$sources` array.

The `$sources` array has the format `['sourceName1' => callableForSource1(), ..., 'sourceNameN' => callableForSourceN()]` 

If `$useSnakeCase` is `true` the `$sourceField` will be converted to snake case and that value should be the key of the field in the data array.

Example:
```php
<?php
declare(strict_types=1);

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

##### buildConverterGetter

```php
protected static function buildConverterGetter(string $fieldName, callable $converter, bool $useSnakeCase = false): callable
```

Calls `$converter` with the value from the `$fieldName` in the data if found, or return null if not found.

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildConverterDefaultGetter

```php
protected static function buildConverterDefaultGetter(string $fieldName, callable $converter, mixed $default = null, bool $useSnakeCase = false): callable
```

Calls `$converter` with the value from the `$fieldName` in the data if found or with `$default` if not found.

If `$useSnakeCase` is `true` the `$fieldName` will be converted to snake case and that value should be the key of the field in the data array.

##### buildConverterMultiFieldsGetter

```php
protected static function buildConverterMultiFieldsGetter(array $fields, bool $all, callable $converter, bool $useSnakeCase = false): callable
```

Checks if:

- If `$all` parameter is **true**, then **ALL** the fields **must exist** in the data
- If `$all` parameter is **false**, then **AT LEAST ONE** of the fields **must exist in the data**

If one of the previous conditions applies it calls `$converter` with the data.

If `$useSnakeCase` is `true` each field in `$fields` will be converted to snake case and that value should be the key of the field in the data array.

## AbstractItemToArray

This class provide an abstract "item" that implements the `ToArray` interface.

If the members of your class properly implement the `ToArray`, `ToInt` and `ToString` interfaces, this method will then recursively convert each field to a basic PHP representation.
