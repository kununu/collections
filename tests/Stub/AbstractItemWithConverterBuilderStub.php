<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\AbstractItem;

/**
 * @method string name()
 * @method string surName()
 * @method string completeName()
 * @method string address()
 */
final class AbstractItemWithConverterBuilderStub extends AbstractItem
{
    protected const GETTER_PREFIX = '';

    protected const PROPERTIES = [
        'name',
        'surName',
        'completeName',
        'address',
    ];

    protected static function getBuilders(): array
    {
        return [
            'name'         => self::buildConverterGetter(
                'name',
                static fn(string $name): string => 'Mr ' . $name,
            ),
            'surName'      => self::buildConverterDefaultGetter(
                'surName',
                static fn(string $surName): string => 'von ' . $surName,
                'UNKNOWN'
            ),
            'completeName' => self::buildConverterMultiFieldsGetter(
                ['name', 'surName'],
                true,
                static fn(array $data): string => $data['name'] . ' ' . $data['surName']
            ),
            'address' => self::buildConverterMultiFieldsGetter(
                ['address', 'country'],
                false,
                static fn(array $data): string => $data['address'] . ' ' . ($data['country'] ?? 'WORLD')
            ),
        ];
    }
}
