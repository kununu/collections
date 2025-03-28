<?php
declare(strict_types=1);

namespace Kununu\Collection\AbstractItemBuilderTraits;

use Kununu\Collection\Convertible\FromArray;

trait FromArrayBuildersTrait
{
    use GenericBuildersTrait;

    protected static function buildFromArrayGetter(
        string $fieldName,
        string $fromArrayClass,
        ?FromArray $default = null,
        bool $useSnakeCase = false,
    ): callable {
        $fieldName = $useSnakeCase ? self::camelToSnake($fieldName) : $fieldName;

        return static fn(array $data): ?FromArray => match (true) {
            !is_a($fromArrayClass, FromArray::class, true) => null,
            isset($data[$fieldName])                       => self::invoke(
                $fromArrayClass,
                'fromArray',
                $data[$fieldName]
            ),
            default                                        => $default,
        };
    }
}
