# KeyValue

The [KeyValue](../src/KeyValue.php) is a DTO designed to act like a simple key/value collection.

The class is an `iterable`, is countable and can be accessed like an array.

## Methods

### fromArray

```php
public static function fromArray(array $data): self;
```

Creates an instance from an `array`.

`fromArray` is just a shortcut to `fromIterable` where the argument is typed to an array.

### fromIterable

Creates an instance from an `iterable`.

```php
public static function fromIterable(iterable $iterable): self;
```

### values

Returns the **values** of the internal array.

```php
public function values(): array
```

### keys

Returns the **keys** of the internal array.

```php
public function keys(): array
```

### get

Gets a **value** by a **key**, returning `$default` value if not found (which is `null` by default).

```php
public function get(int|string $key, mixed $default = null): mixed
```

### set

Sets a **value** by a **key**.

```php
public function set(int|string $key, mixed $value): self
```

### has

Checks if a **value** for a **key** exists.

```php
public function has(int|string $key): bool
```

### remove

Removes a **value** for a **key** if it exists.

```php
public function remove(int|string $key): self
```

### toArray

Returns the internal array holding the key/value collection.

```php
public function toArray(): array
```

### count

Returns the number of values in the collection.

```php
public function count(): int
```

## Example

```php
<?php
declare(strict_types=1);

use Kununu\Collection\KeyValue;

$data = [
    'id' => 100,
    'name' => 'My Name',
    'age' => 18,
];

$values = KeyValue::fromArray($data);

// Getting internal array:
$values->toArray(); // $data

// Getting keys:
$values->keys(); // ['id', 'name', 'age']

// Getting values:
$values->values(); // [100, 'My Name', 18]

// Counting values:
$values->count(); // 3 (via count method)
count($values); // 3 (via PHP count function)

// Setting values:
$values->set('name', 'John Doe'); // Via set method
$values['age'] = 21; // Using array access

// Using an iterator
foreach ($values as $key => $value) {
    echo PHP_EOL;
    echo 'Key = ', $key, PHP_EOL;
    echo 'Value = ', $value, PHP_EOL;
}

// Checking if keys are present
$values->has('salary'); // false
$values->has('age'); // true

// Getting values 
$values->get('id'); // 100 (via get method)
$values->get('age'); // 21 (via get method)
$values->get('salary', 0.0); // 0.0 (via get method, not found so returning default value)
$values['id']; // 100 (via array access)
$values['salary']; // null (via array access)

// Removing values
$values->remove('age'); // Via remove method
unset($values['id']); // Via array access
$values->has('age'); // false
$values->has('id'); // false

```

---

[Back to Index](../README.md)
