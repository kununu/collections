<?php
declare(strict_types=1);

namespace Kununu\Collection;

use Kununu\Collection\Filter\CollectionFilter;
use Kununu\Collection\Filter\FilterItem;

trait FilterableCollectionTrait
{
    use CollectionTrait;

    public function filter(CollectionFilter $filter): self|static
    {
        $filteredResult = new static();
        foreach ($this as $item) {
            if ($item instanceof FilterItem && $filter->isSatisfiedBy($item)) {
                $filteredResult->add($item);
            }
        }

        return $filteredResult;
    }

    /**
     * Groups items in the collection by a series of filters
     * Items in the collection must implement the FilterItem to be considered for grouping
     *
     *  [
     *      'filter_1_key' => [
     *          'item_key_1' => item object 1
     *              ...
     *          'item_key_N' => item object X
     *      ],
     *      'filter_2_key' => [
     *          'item_key_1' => item object 1
     *              ...
     *          'item_key_N' => item object Y
     *      ],
     *     ...
     *  ]
     */
    public function groupBy(bool $removeEmptyGroups, CollectionFilter ...$filters): array
    {
        $groups = [];
        foreach ($filters as $filter) {
            $groups[$filter->key()] = [];
        }

        foreach ($this as $item) {
            foreach ($filters as $filter) {
                if ($item instanceof FilterItem && $filter->isSatisfiedBy($item)) {
                    $groups[$filter->key()][$item->groupByKey($filter->customGroupByData())] = $item;
                }
            }
        }

        return $removeEmptyGroups ? array_filter($groups) : $groups;
    }
}
