# Convertible

The following interfaces are defined to easy the process of recursively your collection data to basic PHP items:

## toArray

This interface defines how to convert a collection item (or a collection itself) to an array:

```php
interface ToArray
{
    public function toArray(): array;
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

## toString

This interface defines how to convert a collection item to a string:

```php
interface ToString
{
    public function toString(): string;
}
```
