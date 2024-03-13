<?php
declare(strict_types=1);

namespace Kununu\Collection;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Kununu\Collection\Convertible\FromArray;

trait AbstractItemBuildersTrait
{
    protected static function buildStringGetter(
        string $fieldName,
        ?string $default = null,
        bool $useSnakeCase = false
    ): callable {
        return self::buildGetterOptionalField(
            $fieldName,
            fn($value): string => (string) $value,
            $default,
            $useSnakeCase
        );
    }

    protected static function buildRequiredStringGetter(string $fieldName, bool $useSnakeCase = false): callable
    {
        return self::buildGetterRequiredField(
            $fieldName,
            static fn(mixed $value): string => (string) $value,
            $useSnakeCase
        );
    }

    protected static function buildBoolGetter(
        string $fieldName,
        ?bool $default = null,
        bool $useSnakeCase = false
    ): callable {
        return self::buildGetterOptionalField(
            $fieldName,
            static fn(mixed $value): bool => (bool) $value,
            $default,
            $useSnakeCase
        );
    }

    protected static function buildRequiredBoolGetter(string $fieldName, bool $useSnakeCase = false): callable
    {
        return self::buildGetterRequiredField(
            $fieldName,
            static fn(mixed $value): bool => (bool) $value,
            $useSnakeCase
        );
    }

    protected static function buildIntGetter(
        string $fieldName,
        ?int $default = null,
        bool $useSnakeCase = false
    ): callable {
        return self::buildGetterOptionalField(
            $fieldName,
            static fn(mixed $value): int => (int) $value,
            $default,
            $useSnakeCase
        );
    }

    protected static function buildRequiredIntGetter(string $fieldName, bool $useSnakeCase = false): callable
    {
        return self::buildGetterRequiredField(
            $fieldName,
            static fn(mixed $value): int => (int) $value,
            $useSnakeCase
        );
    }

    protected static function buildFloatGetter(
        string $fieldName,
        ?float $default = null,
        bool $useSnakeCase = false
    ): callable {
        return self::buildGetterOptionalField(
            $fieldName,
            static fn(mixed $value): float => (float) $value,
            $default,
            $useSnakeCase
        );
    }

    protected static function buildRequiredFloatGetter(string $fieldName, bool $useSnakeCase = false): callable
    {
        return self::buildGetterRequiredField(
            $fieldName,
            static fn(mixed $value): float => (float) $value,
            $useSnakeCase
        );
    }

    protected static function buildDateTimeGetter(
        string $fieldName,
        string $dateFormat = AbstractItem::DATE_FORMAT,
        ?DateTimeInterface $default = null,
        bool $useSnakeCase = false
    ): callable {
        return self::buildGetterOptionalField(
            $fieldName,
            static fn(mixed $value): ?DateTimeInterface => DateTime::createFromFormat($dateFormat, $value) ?: $default,
            $default ? DateTime::createFromInterface($default) : null,
            $useSnakeCase
        );
    }

    protected static function buildRequiredDateTimeGetter(
        string $fieldName,
        string $dateFormat = AbstractItem::DATE_FORMAT,
        bool $useSnakeCase = false
    ): callable {
        return self::buildGetterRequiredField(
            $fieldName,
            static fn(mixed $value): DateTimeInterface => DateTime::createFromFormat($dateFormat, $value),
            $useSnakeCase
        );
    }

    protected static function buildDateTimeImmutableGetter(
        string $fieldName,
        string $dateFormat = AbstractItem::DATE_FORMAT,
        ?DateTimeInterface $default = null,
        bool $useSnakeCase = false
    ): callable {
        return self::buildGetterOptionalField(
            $fieldName,
            static fn(mixed $value): ?DateTimeInterface => DateTimeImmutable::createFromFormat($dateFormat, $value)
                ?: $default,
            $default ? DateTimeImmutable::createFromInterface($default) : null,
            $useSnakeCase
        );
    }

    protected static function buildRequiredDateTimeImmutableGetter(
        string $fieldName,
        string $dateFormat = AbstractItem::DATE_FORMAT,
        bool $useSnakeCase = false
    ): callable {
        return self::buildGetterRequiredField(
            $fieldName,
            static fn(mixed $value): DateTimeInterface => DateTimeImmutable::createFromFormat($dateFormat, $value),
            $useSnakeCase
        );
    }

    protected static function buildGetterOptionalField(
        string $fieldName,
        callable $converter,
        mixed $default = null,
        bool $useSnakeCase = false
    ): callable {
        $fieldName = $useSnakeCase ? self::camelToSnake($fieldName) : $fieldName;

        return static fn(array $data): mixed => isset($data[$fieldName]) ? $converter($data[$fieldName]) : $default;
    }

    protected static function buildGetterRequiredField(
        string $fieldName,
        callable $converter,
        bool $useSnakeCase = false
    ): callable {
        $fieldName = $useSnakeCase ? self::camelToSnake($fieldName) : $fieldName;

        return static fn(array $data) => match (isset($data[$fieldName])) {
            true    => $converter($data[$fieldName]),
            default => throw new InvalidArgumentException(sprintf('Missing "%s" field', $fieldName))
        };
    }

    protected static function buildFromArrayGetter(
        string $fieldName,
        string $fromArrayClass,
        ?FromArray $default = null,
        bool $useSnakeCase = false
    ): callable {
        $fieldName = $useSnakeCase ? self::camelToSnake($fieldName) : $fieldName;

        return static fn(array $data): ?FromArray => match (true) {
            !is_a($fromArrayClass, FromArray::class, true) => null,
            isset($data[$fieldName])                       => self::invoke(
                $fromArrayClass,
                'fromArray',
                $data[$fieldName]
            ),
            default                                        => $default
        };
    }

    protected static function buildCollectionGetter(
        string $fieldName,
        string $collectionClass,
        ?AbstractCollection $default = null,
        bool $useSnakeCase = false
    ): callable {
        $fieldName = $useSnakeCase ? self::camelToSnake($fieldName) : $fieldName;

        return static fn(array $data): ?AbstractCollection => match (true) {
            !is_a($collectionClass, AbstractCollection::class, true) => null,
            isset($data[$fieldName])                                 => self::invoke(
                $collectionClass,
                'fromIterable',
                $data[$fieldName]
            ),
            default                                                  => $default
        };
    }

    protected static function buildConditionalGetter(
        string $sourceField,
        array $sources,
        bool $useSnakeCase = false
    ): callable {
        $sourceField = $useSnakeCase ? self::camelToSnake($sourceField) : $sourceField;

        return static function(array $data) use ($sourceField, $sources): mixed {
            foreach ($sources as $source => $getter) {
                if ($source === ($data[$sourceField] ?? null)) {
                    return is_callable($getter) ? $getter($data) : null;
                }
            }

            return null;
        };
    }

    private static function camelToSnake(string $string): string
    {
        return mb_strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }

    private static function invoke(string $class, string $method, mixed $value): mixed
    {
        return call_user_func_array([$class, $method], [$value]);
    }
}
