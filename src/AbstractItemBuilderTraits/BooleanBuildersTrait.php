<?php
declare(strict_types=1);

namespace Kununu\Collection\AbstractItemBuilderTraits;

trait BooleanBuildersTrait
{
    use GenericBuildersTrait;

    protected static function buildBoolGetter(
        string $fieldName,
        ?bool $default = null,
        bool $useSnakeCase = false,
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
}
