# Convertible

The following interfaces are defined to easy the process of creating/converting collections/collection items to basic PHP types.

## FromIterable

This interface defines how to create a collection item from an iterable:

```php
interface FromIterable
{
    public static function fromIterable(iterable $data): self|static;
}
```

## FromArray

This interface defines how to create a collection item from an array:

```php
interface FromArray
{
    public static function fromArray(array $data): self|static;
}
```

## ToArray

This interface defines how to convert a collection item (or a collection itself) to an array:

```php
interface ToArray
{
    public function toArray(): array;
}
```

## FromInt

This interface defines how to create a collection item from an integer:

```php
interface FromInt
{
    public static function fromInt(int $value): self|static;
}
```

## ToInt

This interface defines how to convert a collection item to an integer:

```php
interface ToInt
{
    public function toInt(): int;
}
```

## FromString

This interface defines how to create a collection item from a string:

```php
interface FromString
{
    public static function fromString(string $value): self|static;
}
```

## ToString

This interface defines how to convert a collection item to a string:

```php
interface ToString
{
    public function toString(): string;
}
```

## FromStringable

This interface defines how to create a collection item from a string or from a `Stringable` object:

```php
interface FromStringable
{
    public static function fromStringable(string|Stringable $value): self|static;
}
```

---

[Back to Index](../README.md)
