<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\AbstractItem;

/**
 * @method DTOCollectionStub|null collection()
 * @method DTOCollectionStub|null notCollection()
 * @method DTOCollectionStub|null defaultCollection()
 */
final class AbstractItemWithCollectionsStub extends AbstractItem
{
    protected const string GETTER_PREFIX = '';

    protected const array PROPERTIES = [
        'collection',
        'notCollection',
        'defaultCollection',
    ];

    protected static function getBuilders(): array
    {
        return [
            'collection'        => self::buildCollectionGetter('collection', DTOCollectionStub::class),
            'notCollection'     => self::buildCollectionGetter('notCollection', 'ThisIsNotACollectionClass'),
            'defaultCollection' => self::buildCollectionGetter(
                'defaultCollection',
                DTOCollectionStub::class,
                new DTOCollectionStub()
            ),
        ];
    }
}
