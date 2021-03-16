<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use DateTime;
use Kununu\Collection\AbstractItem;

/**
 * @method null|int getId()
 * @method null|string getName()
 * @method null|DateTime getCreatedAt()
 * @method null|string getExtraFieldNotUsedInBuild()
 * @method null|string getSimpleName()
 * @method null|bool getVerified()
 * @method null|int getIndustryId()
 * @method AbstractItemStub setId(?int $id)
 * @method AbstractItemStub setName(?string $name)
 * @method AbstractItemStub setCreatedAt(?DateTime $createdAt)
 * @method AbstractItemStub setSimpleName(?string $simpleName)
 * @method AbstractItemStub setVerified(?bool $verified)
 * @method AbstractItemStub setIndustryId(?int $industryId)
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
    ];

    protected static function getBuilders(): array
    {
        return [
            'id'         => function(array $data) {
                return $data['id'] ?? null;
            },
            'name'       => function(array $data) {
                return $data['name'] ?? null;
            },
            'createdAt'  => function(array $data) {
                return $data['createdAt'] ?? null;
            },
            'simpleName' => self::buildStringGetter('simpleName'),
            'verified'   => self::buildBoolGetter('verified'),
            'industryId' => self::buildIntGetter('industryId'),
        ];
    }
}
