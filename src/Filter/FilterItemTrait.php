<?php
declare(strict_types=1);

namespace Kununu\Collection\Filter;

trait FilterItemTrait
{
    private static function filterIsSatisfiedByItem(CollectionFilter $filter, mixed $item): bool
    {
        return $item instanceof FilterItem && $filter->isSatisfiedBy($item);
    }
}
