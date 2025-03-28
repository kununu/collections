<?php
declare(strict_types=1);

namespace Kununu\Collection\AbstractItemBuilderTraits;

trait FloatBuildersTrait
{
    use GenericBuildersTrait;

    protected static function buildFloatGetter(
        string $fieldName,
        ?float $default = null,
        bool $useSnakeCase = false,
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
}
