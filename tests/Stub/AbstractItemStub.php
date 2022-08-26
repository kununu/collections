<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use DateTime;
use Kununu\Collection\AbstractItem;

/**
 * @method int              getId()
 * @method string|null      getName()
 * @method DateTime|null    getCreatedAt()
 * @method string|null      getExtraFieldNotUsedInBuild()
 * @method string|null      getSimpleName()
 * @method bool             getVerified()
 * @method int|null         getIndustryId()
 * @method float|null       getSalary()
 * @method AbstractItemStub setId(?int $id)
 * @method AbstractItemStub setName(?string $name)
 * @method AbstractItemStub setCreatedAt(?DateTime $createdAt)
 * @method AbstractItemStub setSimpleName(?string $simpleName)
 * @method AbstractItemStub setVerified(?bool $verified)
 * @method AbstractItemStub setIndustryId(?int $industryId)
 * @method AbstractItemStub setSalary(?float $salary)
 */
final class AbstractItemStub extends AbstractItem
{
    protected const PROPERTIES = [
        'id',
        'name',
        'createdAt',
        'extraFieldNotUsedInBuild',
        'simpleName',
        'verified',
        'industryId',
        'salary',
    ];

    protected static function getBuilders(): array
    {
        return [
            'id'         => self::buildIntGetter('id', 0),
            'name'       => self::buildStringGetter('name'),
            'createdAt'  => self::buildDateTimeGetter('createdAt'),
            'simpleName' => self::buildStringGetter('simpleName'),
            'verified'   => self::buildBoolGetter('verified', false),
            'industryId' => self::buildIntGetter('industryId'),
            'salary'     => self::buildFloatGetter('salary', 1000.0),
        ];
    }
}
