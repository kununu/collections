<?php
declare(strict_types=1);

namespace Kununu\Collection\AbstractItemBuilderTraits;

trait StringBuildersTrait
{
    use GenericBuildersTrait;

    protected static function buildStringGetter(
        string $fieldName,
        ?string $default = null,
        bool $useSnakeCase = false,
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
}
