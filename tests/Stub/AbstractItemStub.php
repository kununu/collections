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
 * @method AbstractItemStub setId(?int $id)
 * @method AbstractItemStub setName(?string $name)
 * @method AbstractItemStub setCreatedAt(?DateTime $createdAt)
 */
final class AbstractItemStub extends AbstractItem
{
    protected const PROPERTIES = [
        'id',
        'name',
        'createdAt',
        'extraFieldNotUsedInBuild',
    ];

    protected static function getBuilders(): array
    {
        return [
            'id'        => function(array $data) {
                return $data['id'] ?? null;
            },
            'name'      => function(array $data) {
                return $data['name'] ?? null;
            },
            'createdAt' => function(array $data) {
                return $data['createdAt'] ?? null;
            },
        ];
    }
}
