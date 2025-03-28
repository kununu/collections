<?php
declare(strict_types=1);

namespace Kununu\Collection\AbstractItemBuilderTraits;

use InvalidArgumentException;

trait GenericBuildersTrait
{
    protected static function buildGetterOptionalField(
        string $fieldName,
        callable $converter,
        mixed $default = null,
        bool $useSnakeCase = false,
    ): callable {
        $fieldName = $useSnakeCase ? self::camelToSnake($fieldName) : $fieldName;

        return static fn(array $data): mixed => isset($data[$fieldName]) ? $converter($data[$fieldName]) : $default;
    }

    protected static function buildGetterRequiredField(
        string $fieldName,
        callable $converter,
        bool $useSnakeCase = false,
    ): callable {
        $fieldName = $useSnakeCase ? self::camelToSnake($fieldName) : $fieldName;

        return static fn(array $data) => match (isset($data[$fieldName])) {
            true    => $converter($data[$fieldName]),
            default => throw new InvalidArgumentException(sprintf('Missing "%s" field', $fieldName)),
        };
    }

    protected static function buildConditionalGetter(
        string $sourceField,
        array $sources,
        bool $useSnakeCase = false,
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
