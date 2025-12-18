# AbstractEnumKeyValue

The [AbstractEnumKeyValue](../src/EnumKeyValue/AbstractEnumKeyValue.php) is very similar to the [KeyValue DTO](key-value.md).

Like `KeyValue`, it is a base class for DTO designed to act like a simple key/value collection, but where the possible keys must be pre-defined (like in an **Enumeration**, hence the name).

The class is an `iterable` and is also countable.

## Concepts

### EnumKeyInterface

In order to limit the valid keys, you will need to define an enumeration (or an enumeration-like regular class) and make it implement the `EnumKeyInterface`.

```php
public function key(): string;

public function required(): bool;
```

- The `key` method must return a distinct value for each allowed key.
    - In the case of an enumeration you can simply return the `name` property (or the `value` if you are using a string backed enumeration).
    - Be aware of this as the `key` value will be used for magic methods (described bellow in this document)

- The `required` method should return a boolean value that will be used to indicate that this key is mandatory 
    - The `get` method will throw an exception if the key is required, but it's not set in the key value storage (e.g. a required value must exist to be used)
      - The `remove` method will throw an exception if the key is required and set in the key value storage (e.g. you can't remove a required value)
  - If you don't want to use this feature, just create a method that always returns `false`
  - If you want **ALL** the keys to be required just make it always return `true`.
  - The [Required](../src/EnumKeyValue/Required.php) attribute is also included if you want to use it, but you will have to use reflection to extract it from your enumeration cases.
    - The [EnumKeyRequiredTrait](../src/EnumKeyValue/EnumKeyRequiredTrait.php) provides this, but it will only work with pure PHP enumerations, not enumeration-like classes.

### createKeyFromString

```php
abstract protected static function createKeyFromString(string $key): EnumKeyInterface
```

Your child `EnumKeyValue` class must implement this abstract static method. This should create your enumeration (or enumeration-like class) instance from the key.

If you are using a backed string enumeration you can easily use the `BackedEnum::from` method.

```php
<?php
declare(strict_types=1);

use Kununu\Collection\EnumKeyValue\AbstractEnumKeyValue;
use Kununu\Collection\EnumKeyValue\EnumKeyInterface;
use Kununu\Collection\EnumKeyValue\EnumKeyRequiredTrait;
use Kununu\Collection\EnumKeyValue\Required;

enum MyKeys: string implements EnumKeyInterface
{
    use EnumKeyRequiredTrait;

    #[Required]
    case Age = 'Age';
    #[Required]
    case Name = 'Name';
    case Salary = 'Salary';
    case Address = 'Address';
    
    public function key(): string
    {
        return $this->value;
    }
}

final class MyKeyValue extends AbstractEnumKeyValue
{
    protected static function createKeyFromString(string $key): MyKeys
    {
        return MyKeys::from($key)
    }
}
```

If you don't want to use a backed enumeration or don't want to use the `value` of the enumeration then you need to implement a method to create an enumeration value from the case name.

Since PHP didn't provide a way to do this you can use the [EnumFromNameTrait](../src/EnumKeyValue/EnumFromNameTrait.php) 

This will allow you to use the **case name** as the string representation of the key.

```php
<?php
declare(strict_types=1);

use Kununu\Collection\EnumKeyValue\AbstractEnumKeyValue;
use Kununu\Collection\EnumKeyValue\EnumKeyInterface;
use Kununu\Collection\EnumKeyValue\EnumFromNameTrait;
use Kununu\Collection\EnumKeyValue\EnumKeyRequiredTrait;
use Kununu\Collection\EnumKeyValue\Required;

enum MyKeys: string implements EnumKeyInterface
{
    use EnumFromNameTrait;
    use EnumKeyRequiredTrait;

    #[Required]
    case Age;
    #[Required]
    case Name;
    case Salary;
    case Address;
    
    public function key(): string
    {
        return $this->value;
    }
}

final class MyKeyValue extends AbstractEnumKeyValue
{
    protected static function createKeyFromString(string $key): EnumKeyInterface
    {
        return MyKeys::fromName($key)
    }
}
```

Like mentioned, even if this kind of DTO is suited to be used with enumerations, you can use it with regular PHP classes:

```php
<?php
declare(strict_types=1);

use Kununu\Collection\EnumKeyValue\AbstractEnumKeyValue;
use Kununu\Collection\EnumKeyValue\EnumKeyInterface;

final class MyKeys implements EnumKeyInterface
{
    public const string AGE = 'Age';
    public const string NAME = 'Name';
    public const string SALARY = 'Salary';
    public const string ADDRESS = 'Address';

    private const array VALID = [
        self::AGE,
        self::NAME,
        self::SALARY,
        self::ADDRESS,
    ];

    private function __construct(private readonly string $value)
    {
        if (!in_array($this->value, self::VALID)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid value', $value));
        }
    }
    
    public static function fromName(string $name): self
    {
        return new self($name);
    }

    public function required(): bool
    {
        return match ($this->value) {
            self::AGE,
            self::NAME => true,
            default    => false 
        };
    }
    
    public function key(): string
    {
        return $this->value;
    }
}

final class MyKeyValue extends AbstractEnumKeyValue
{
    protected static function createKeyFromString(string $key): EnumKeyInterface
    {
        return MyKeys::fromName($key);
    }
}
```

### Methods

#### fromArray

```php
/** @param array<string, mixed> $data */
public static function fromArray(array $data): self|static
```

Creates an instance from an `array`. This is just a shortcut to `fromIterable` where the argument is typed to an array.

#### fromIterable

Creates an instance from an `iterable`.

```php
/** @param array<string, mixed> $data */
public static function fromIterable(iterable $data): self|static
```

Note that the keys in the source are **strings** that must be valid ones (e.g. your `createKeyFromString` should be able to create a valid instance of your enumeration/class).

#### count

Returns the number of values in the key/value collection.

```php
public function count(): int
```

#### get

Gets a **value** by a **key**, returning `$default` value if not found (which is `null` by default).

```php
/** @throws RequiredKeyMissingException */
public function get(string|EnumKeyInterface $key, mixed $default = null): mixed
```

You can use a string `$key` that will call your `createKeyFromString` to create an enumeration from that value.

If the key is **required**, and it's not set, a `RequiredKeyMissingException` is thrown.

#### has

Checks if a **value** for a **key** exists.

```php
public function has(int|string $key): bool
```

You can use a string `$key` that will call your `createKeyFromString` to create an enumeration from that value.

#### keys

Returns the **keys** of the internal array.

```php
public function keys(bool $asString = true): array
```

By default, it will return the keys as **string** values, but you pass `false` and it will return an array composed of your keys enumeration values.

#### remove

Removes a **value** for a **key** if it exists.

```php
/** @throws RemovingRequiredKeyException */
public function remove(string|EnumKeyInterface $key): self
```

You can use a string `$key` that will call your `createKeyFromString` to create an enumeration from that value.

If the key is **required**, and it is set, a `RemovingRequiredKeyException` is thrown.

#### set

Sets a **value** by a **key**.

```php
public function set(string|EnumKeyInterface $key, mixed $value): self
```

You can use a string `$key` that will call your `createKeyFromString` to create an enumeration from that value.

#### toArray

Returns the internal array holding the key/value collection.

```php
public function toArray(): array
```

#### values

Returns the **values** of the internal array.

```php
public function values(): array
```

### Magic Methods

Like what is achieved in the [AbstractItem](abstract-item.md), and since we will have a limited number of possible keys for the `EnumKeyValue`, we will have the possibility to dynamically call methods in the key value.

These will be wrapper for the possible operations for each key:

- Get value of the key (`get` method)
- Check if it has a value for the key (`has` method)
- Remove the value for the key (`remove` method)
- Set the value for the key (`set` method)

Based on the keys defined on your subclass the class will accept calls to each of them with the format:

Key: `MyKey`

Set: `$keyValues->setMyKey($value);`

Get: `$value = $keyValues->getMyKey()`

Has: `$exists = $keyValues->hasMyKey()`

Remove: `$keyValues->removeMyKey()`

Also, for a better experience with the magic methods, you can declare PHPDoc blocks in your class (see in the example at the bottom of the document).

If you want to use different prefixes for the methods you can! Just override any of these constants:

```php
// Default values:
protected const string HAS_PREFIX = 'has';
protected const string SETTER_PREFIX = 'set';
protected const string GETTER_PREFIX = 'get';
protected const string REMOVE_PREFIX = 'remove';
```

If you want to use `exists` for the `has` methods, `delete` for the `remove` and no prefix for the `get` methods:

```php
// Default values:
protected const string HAS_PREFIX = 'exists';
protected const string GETTER_PREFIX = '';
protected const string REMOVE_PREFIX = 'delete';
```

Now you could use these:

Key: `MyKey`

Set: `$keyValues->setMyKey($value);`

Get: `$value = $keyValues->myKey()`

Has: `$exists = $keyValues->existsMyKey()`

Remove: `$keyValues->deleteMyKey()`

- Format Option

By default, the key value as string, extracted from the magic method name will have the first letter capitalized.

E.g. If your getter prefix is `get` and your key is `MyKey`, then the magic method `getMyKey` will extract `MyKey` from the name.

Now imagine your getter prefix is empty (`''`) and the magic method is `myKey`.

Without the capitalization the extracted string would be `myKey` which could cause your `createKeyFromString` to fail (if you are creating your enumeration instance by case name, for example).

So the library will transform it to `MyKey`.

If this is not suitable for your particular enumeration/enumeration-like class then you can change the behavior by overriding the `FORMAT_OPTION` constant:

```php
protected const FormatOption FORMAT_OPTION = FormatOption::UpperCaseFirst;
```

To any of these possible values:

```php
<?
declare(strict_types=1);

enum FormatOption
{
    // String will be kept as it is after trimming the prefix
    case None;
    // String will be in lowercase after trimming the prefix
    case LowerCase;
    // String will have the first character lower-cased after trimming the prefix
    case LowerCaseFirst;
    // String will be in uppercase after trimming the prefix
    case UpperCase;
    // String will have the first character upper-cased after trimming the prefix
    case UpperCaseFirst;
}
```

## Example

```php
<?php
declare(strict_types=1);

use Kununu\Collection\EnumKeyValue\AbstractEnumKeyValue;
use Kununu\Collection\EnumKeyValue\EnumKeyInterface;
use Kununu\Collection\EnumKeyValue\EnumFromNameTrait;
use Kununu\Collection\EnumKeyValue\EnumKeyRequiredTrait;
use Kununu\Collection\EnumKeyValue\Required;

enum Key implements EnumKeyInterface
{
    use EnumFromNameTrait;
    use EnumKeyRequiredTrait;

    #[Required]
    case Id;
    case Age;
    #[Required]
    case Name;
    case Salary;
    case Address;
    
    public function key(): string
    {
        return $this->value;
    }
}

/**
 * @method int         getId()
 * @method int|null    getAge()
 * @method string|null getName()
 * @method float|null  getSalary()
 * @method string|null getAddress()
 * @method bool        hasId()
 * @method bool        hasAge()
 * @method bool        hasSalary()
 * @method bool        hasAddress()
 * @method self        removeId()
 * @method self        removeAge()
 * @method self        removeName()
 * @method self        removeAddress()
 * @method self        setId(int $id)
 * @method self        setAge(int|null $age)
 * @method self        setName(string|null $name)
 * @method self        setSalary(float|null $salary)
 * @method self        setAddress(string|null $address)
 */
final class KeyValue extends AbstractEnumKeyValue
{
    protected static function createKeyFromString(string $key): Key
    {
        return Key::fromName($key)
    }
}

$data = [
    'Id' => 100,
    'Name' => 'My Name',
    'Age' => 18,
];

$values = KeyValue::fromArray($data);

// Getting internal array:
$values->toArray(); // $data

// Getting keys:
$values->keys(); // ['Id', 'Name', 'Age']
$values->keys(false); // [Keys::Id, Keys::Name, Keys::Age]

// Getting values:
$values->values(); // [100, 'My Name', 18]

// Counting values:
$values->count(); // 3 (via count method)
count($values); // 3 (via PHP count function)

// Setting values:
$values->set('Name', 'John Doe'); // Via set method with a (valid) string as key  
$values->set(Key::Address, 'Unknown Rd 12345'); // Via set method with an enumeration value as key
$values->setAge(21); // Via a magic method

// Using an iterator
foreach ($values as $key => $value) {
    echo PHP_EOL;
    echo 'Key = ', $key, PHP_EOL;
    echo 'Value = ', $value, PHP_EOL;
}

// Checking if keys are present
$values->has('Salary'); // false
$values->has(Key::Salary); // false
$values->has(Key::Age); // true

// Getting values 
$values->get('Id'); // 100 (via get method)
$values->getAge(); // 21 (via magic method)
$values->get('Salary', 0.0); // 0.0 (via get method, not found so returning default value passed in the argument)
$values->getSalary(); // null (via magic method, not found so returning null)

// Removing values
$values->remove('Age'); // Via remove method
$values->removeAddress(); // Via magic method

$values->hasAge(); // false
$values->has(Key::Address); // false

$values->removeId(); // Exception because key is required, it's set, so it can't be removed

// Required keys
$new = new KeyValue();

$new->hasAddress(); // false
$new->getAddress(); // null
$new->hasName(); // false
$new->get(Key::Name); // Exception because key is required and it's not set

```

---

[Back to Index](../README.md)
