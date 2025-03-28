<?php
declare(strict_types=1);

namespace Kununu\Collection\AbstractItemBuilderTraits;

use Kununu\Collection\AbstractCollection;

trait CollectionBuildersTrait
{
    use GenericBuildersTrait;

    protected static function buildCollectionGetter(
        string $fieldName,
        string $collectionClass,
        ?AbstractCollection $default = null,
        bool $useSnakeCase = false,
    ): callable {
        $fieldName = $useSnakeCase ? self::camelToSnake($fieldName) : $fieldName;

        return static fn(array $data): ?AbstractCollection => match (true) {
            !is_a($collectionClass, AbstractCollection::class, true) => null,
            isset($data[$fieldName])                                 => self::invoke(
                $collectionClass,
                'fromIterable',
                $data[$fieldName]
            ),
            default                                                  => $default,
        };
    }
}
