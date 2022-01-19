<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

/**
 * @method ToArrayStub getExtraData()
 */
final class AbstractItemToArrayStub extends AbstractItemToArrayStubBase
{
    protected const PROPERTIES = [
        'extraData',
    ];

    protected static function getBuilders(): array
    {
        return array_merge(
            parent::getBuilders(),
            [
                'extraData' => self::buildGetterRequiredField(
                    'extraData',
                    function(array $value): ToArrayStub {
                        return ToArrayStub::create(
                            $id = ToIntStub::fromInt((int) $value['id']),
                            ToStringStub::create($id, $value['description'])
                        );
                    }
                ),
            ]
        );
    }
}
