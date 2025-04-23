<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use DateTime;
use Kununu\Collection\AbstractItem;

/**
 * @method int      giveMeTheId()
 * @method string   giveMeTheName()
 * @method DateTime giveMeTheCreatedAt()
 * @method bool     giveMeTheVerified()
 * @method DTOStub  giveMeTheCustom()
 * @method float    giveMeTheScore()
 */
final class AbstractItemWithRequiredFieldsStub extends AbstractItem
{
    protected const string GETTER_PREFIX = 'giveMeThe';

    protected const array PROPERTIES = [
        'id',
        'name',
        'createdAt',
        'verified',
        'custom',
        'score',
    ];

    protected static function getBuilders(): array
    {
        return [
            'id'        => self::buildRequiredIntGetter('id'),
            'name'      => self::buildRequiredStringGetter('name'),
            'createdAt' => self::buildRequiredDateTimeGetter('createdAt'),
            'verified'  => self::buildRequiredBoolGetter('verified'),
            'custom'    => self::buildGetterRequiredField('custom', fn($value) => new DTOStub('my_dto', $value)),
            'score'     => self::buildRequiredFloatGetter('score'),
        ];
    }
}
