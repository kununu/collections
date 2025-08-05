# Abstract Collection Test Case

To test your collections you can make your test classes extend the `AbstractCollectionTestCase`.

## Tests

### Test populated collection

```php
public function testCollection(): void
```

This will test:

- The number of items in your populated collection

Override the following constant to the expected count of your populated collection:

```php
protected const int EXPECTED_COUNT = 0;
```

- The class of the item return by the `current` method

Override the following constant to the expected class of items of the collection:

```php
protected const string EXPECTED_ITEM_CLASS = '';
```

- (Optional) The value return by the `toArray` method

By default, it will test this. Override the following constant to `false` if you don't want to test this:

```php
protected const bool TEST_TO_ARRAY = true;
```

The populated collection being tested should be returned by implementing the following method:

```php
abstract protected function createCollection(): Collection
```

You can also do extra custom assertions to your populated collection.

In order to do that you can override the following method:

```php
protected function doExtraAssertions(Collection $collection): void
```

The `$collection` that is passed to the method is the same returned by the `createCollection` method.

## Test empty collection

```php
public function testEmptyCollection(): void
```

This will test:

- That the collection is empty
- That the `current` method return `null`

The empty collection being tested should be returned by implementing the following method:

```php
abstract protected function createEmptyCollection(): Collection
```

## Test adding an invalid value to the collection

```php
public function testAddInvalid(): void
```

This will test:

- That adding an invalid value to a collection will throw an exception

By default, it will test that an `InvalidArgumentException` is thrown. 

Override the following constant if your collection throws a different exception:

```php
protected const string INVALID_EXCEPTION_CLASS = InvalidArgumentException::class;
```

If you also want to expect for a specific exception error message, then override the following constant:

```php
protected const ?string INVALID_ERROR_MESSAGE = null;
```

This test also uses the collection created with the `createEmptyCollection` method. 

## Example

Take a look at [this](../tests/TestCase/AbstractCollectionTestCaseTest.php) test to see an example of using the test case. 

---

[Back to Index](../README.md)
