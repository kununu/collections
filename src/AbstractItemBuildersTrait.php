<?php
declare(strict_types=1);

namespace Kununu\Collection;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;

trait AbstractItemBuildersTrait
{
    protected static function buildStringGetter(string $fieldName, ?string $default = null): callable
    {
        return fn(array $data): ?string => isset($data[$fieldName]) ? (string) $data[$fieldName] : $default;
    }

    protected static function buildRequiredStringGetter(string $fieldName): callable
    {
        return self::buildGetterRequiredField($fieldName, fn($value): string => (string) $value);
    }

    protected static function buildBoolGetter(string $fieldName, ?bool $default = null): callable
    {
        return fn(array $data): ?bool => isset($data[$fieldName]) ? (bool) $data[$fieldName] : $default;
    }

    protected static function buildRequiredBoolGetter(string $fieldName): callable
    {
        return self::buildGetterRequiredField($fieldName, fn($value): bool => (bool) $value);
    }

    protected static function buildIntGetter(string $fieldName, ?int $default = null): callable
    {
        return fn(array $data): ?int => isset($data[$fieldName]) ? (int) $data[$fieldName] : $default;
    }

    protected static function buildRequiredIntGetter(string $fieldName): callable
    {
        return self::buildGetterRequiredField($fieldName, fn($value): int => (int) $value);
    }

    protected static function buildFloatGetter(string $fieldName, ?float $default = null): callable
    {
        return fn(array $data): ?float => isset($data[$fieldName]) ? (float) $data[$fieldName] : $default;
    }

    protected static function buildRequiredFloatGetter(string $fieldName): callable
    {
        return self::buildGetterRequiredField($fieldName, fn($value): float => (float) $value);
    }

    protected static function buildDateTimeGetter(
        string $fieldName,
        string $dateFormat = AbstractItem::DATE_FORMAT,
        ?DateTimeInterface $default = null
    ): callable
    {
        return fn(array $data): ?DateTimeInterface => isset($data[$fieldName])
            ? (DateTime::createFromFormat($dateFormat, $data[$fieldName]) ?: $default)
            : null;
    }

    protected static function buildRequiredDateTimeGetter(
        string $fieldName,
        string $dateFormat = AbstractItem::DATE_FORMAT
    ): callable
    {
        return self::buildGetterRequiredField(
            $fieldName,
            fn($value): DateTimeInterface => DateTime::createFromFormat($dateFormat, $value)
        );
    }

    protected static function buildGetterRequiredField(string $fieldName, callable $converter): callable
    {
        return fn(array $data) => match (true) {
            isset($data[$fieldName]) => $converter($data[$fieldName]),
            default                  => throw new InvalidArgumentException(sprintf('Missing "%s" field', $fieldName))
        };
    }

    protected static function buildCollectionGetter(
        string $fieldName,
        string $collectionClass,
        ?AbstractCollection $default = null
    ): callable
    {
        return fn(array $data): ?AbstractCollection => match (true) {
            !is_a($collectionClass, AbstractCollection::class, true) => null,
            isset($data[$fieldName])                                 => call_user_func_array(
                [$collectionClass, 'fromIterable'],
                [$data[$fieldName]]
            ),
            default => $default
        };
    }

    protected static function buildConditionalGetter(string $sourceField, array $sources): callable
    {
        return function(array $data) use ($sourceField, $sources) {
            foreach ($sources as $source => $getter) {
                if ($source === ($data[$sourceField] ?? null)) {
                    return is_callable($getter) ? $getter($data) : null;
                }
            }

            return null;
        };
    }
}
