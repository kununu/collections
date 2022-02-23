<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\AbstractCollection;
use Kununu\Collection\AutoSortableOffsetSetTrait;

final class AutoSortedCollectionStub extends AbstractCollection
{
    use AutoSortableOffsetSetTrait;

    public function append($value): void
    {
        switch (true) {
            case is_string($value):
            case is_int($value):
                $this->offsetSet($value, $value);
                break;
            default:
                parent::append($value);
        }
    }

    public function toArray(): array
    {
        return $this->mapToArray(false);
    }
}
