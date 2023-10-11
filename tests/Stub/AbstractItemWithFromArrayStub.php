<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\AbstractItem;

/**
 * @method FromArrayStub|null fromArray()
 * @method FromArrayStub|null notFromArray()
 * @method FromArrayStub|null defaultFromArray()
 */
final class AbstractItemWithFromArrayStub extends AbstractItem
{
    protected const GETTER_PREFIX = '';

    protected const PROPERTIES = [
        'fromArray',
        'notFromArray',
        'defaultFromArray',
    ];

    protected static function getBuilders(): array
    {
        return [
            'fromArray'        => self::buildFromArrayGetter('fromArray', FromArrayStub::class),
            'notFromArray'     => self::buildFromArrayGetter('notFromArray', 'ThisIsNotACollectionClass'),
            'defaultFromArray' => self::buildFromArrayGetter(
                'defaultFromArray',
                FromArrayStub::class,
                new FromArrayStub(0, '')
            ),
        ];
    }
}
