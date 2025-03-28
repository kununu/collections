<?php
declare(strict_types=1);

namespace Kununu\Collection\AbstractItemBuilderTraits;

trait ConverterBuildersTrait
{
    use GenericBuildersTrait;

    protected static function buildConverterGetter(
        string $fieldName,
        callable $converter,
        bool $useSnakeCase = false,
    ): callable {
        $fieldName = $useSnakeCase ? self::camelToSnake($fieldName) : $fieldName;

        return static fn(array $data) => isset($data[$fieldName]) ? $converter($data[$fieldName]) : null;
    }

    protected static function buildConverterDefaultGetter(
        string $fieldName,
        callable $converter,
        mixed $default = null,
        bool $useSnakeCase = false,
    ): callable {
        $fieldName = $useSnakeCase ? self::camelToSnake($fieldName) : $fieldName;

        return static fn(array $data) => $converter($data[$fieldName] ?? $default);
    }

    protected static function buildConverterMultiFieldsGetter(
        array $fields,
        bool $all,
        callable $converter,
        bool $useSnakeCase = false,
    ): callable {
        $fields = array_map(
            static fn(string $fieldName): string => $useSnakeCase ? self::camelToSnake($fieldName) : $fieldName,
            $fields
        );

        return static function(array $data) use ($fields, $all, $converter) {
            $found = $all;
            foreach ($fields as $fieldName) {
                $isset = isset($data[$fieldName]);
                $found = $all ? ($found && $isset) : ($found || $isset);
                if ($found && !$all) {
                    break;
                }
            }

            return $found ? $converter($data) : null;
        };
    }
}
