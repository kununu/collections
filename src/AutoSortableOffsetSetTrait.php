<?php
declare(strict_types=1);

namespace Kununu\Collection;

trait AutoSortableOffsetSetTrait
{
    public function offsetSet($key, $value): void
    {
        parent::offsetSet($key, $value);
        $this->ksort();
    }
}
