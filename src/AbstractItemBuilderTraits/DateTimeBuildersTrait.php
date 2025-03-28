<?php
declare(strict_types=1);

namespace Kununu\Collection\AbstractItemBuilderTraits;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Kununu\Collection\AbstractItem;

trait DateTimeBuildersTrait
{
    protected static function buildDateTimeGetter(
        string $fieldName,
        string $dateFormat = AbstractItem::DATE_FORMAT,
        ?DateTimeInterface $default = null,
        bool $useSnakeCase = false,
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
        bool $useSnakeCase = false,
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
        bool $useSnakeCase = false,
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
        bool $useSnakeCase = false,
    ): callable {
        return self::buildGetterRequiredField(
            $fieldName,
            static fn(mixed $value): DateTimeInterface => DateTimeImmutable::createFromFormat($dateFormat, $value),
            $useSnakeCase
        );
    }
}
