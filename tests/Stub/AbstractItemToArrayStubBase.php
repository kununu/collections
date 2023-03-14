<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\AbstractItemToArray;

/**
 * @method int          getId()
 * @method ToStringStub getName()
 * @method ToIntStub    getIndustryId()
 * @method bool         getVerified()
 */
abstract class AbstractItemToArrayStubBase extends AbstractItemToArray
{
    protected const PROPERTIES = [
        'id',
        'name',
        'industryId',
        'verified',
    ];

    protected static function getBuilders(): array
    {
        return [
            'id'         => self::buildIntGetter('id'),
            'name'       => self::buildGetterRequiredField(
                'name',
                fn(string $value): ToStringStub => ToStringStub::create(ToIntStub::fromInt(1000), $value)
            ),
            'verified'   => self::buildBoolGetter('verified'),
            'industryId' => self::buildGetterRequiredField(
                'industryId',
                fn(int $value): ToIntStub => ToIntStub::fromInt($value)
            ),
        ];
    }
}
