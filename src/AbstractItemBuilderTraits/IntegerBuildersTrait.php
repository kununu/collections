<?php
declare(strict_types=1);

namespace Kununu\Collection\AbstractItemBuilderTraits;

trait IntegerBuildersTrait
{
    use GenericBuildersTrait;

    protected static function buildIntGetter(
        string $fieldName,
        ?int $default = null,
        bool $useSnakeCase = false,
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
}
