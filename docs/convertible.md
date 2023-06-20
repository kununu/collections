# Convertible

The following interfaces are defined to easy the process of creating/converting collections/collection items to basic  PHP types.

## fromArray

This interface defines how to create a collection item from an array:

```php
interface FromArray
{
    public static function fromArray(array $data): self|static;
}
```

## toArray

This interface defines how to convert a collection item (or a collection itself) to an array:

```php
interface ToArray
{
    public function toArray(): array;
}
```

## fromInt

This interface defines how to create a collection item from an integer:

```php
interface FromInt
{
    public static function fromInt(int $value): self|static;
}
```

## toInt

This interface defines how to convert a collection item to an integer:

```php
interface ToInt
{
    public function toInt(): int;
}
```

## fromString

This interface defines how to create a collection item from a string:

```php
interface FromString
{
    public static function fromString(string $value): self|static;
}
```

## toString

This interface defines how to convert a collection item to a string:

```php
interface ToString
{
    public function toString(): string;
}
```
