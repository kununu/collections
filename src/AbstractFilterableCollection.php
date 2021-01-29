<?php declare(strict_types=1);

namespace Kununu\Collection;

use Kununu\Collection\Filter\CollectionFilter;

abstract class AbstractFilterableCollection extends AbstractCollection
{
    use FilterableCollectionTrait {
        filter as traitFilter;
    }

    /**
     * @param CollectionFilter $filter
     *
     * @return CollectionTrait|static
     */
    public function filter(CollectionFilter $filter): self
    {
        return self::traitFilter($filter);
    }
}
