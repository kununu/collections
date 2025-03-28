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
        match (true) {
            is_string($value),
            is_int($value) => $this->offsetSet($value, $value),
            default        => parent::append($value),
        };
    }

    public function toArray(): array
    {
        return $this->mapToArray(false);
    }
}
