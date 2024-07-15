<?php
declare(strict_types=1);

namespace Kununu\Collection;

trait AutoSortableOffsetSetTrait
{
    public function offsetSet(mixed $key, mixed $value): void
    {
        parent::offsetSet($key, $value);

        $this->ksort();
    }
}
