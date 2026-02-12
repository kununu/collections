<?php
declare(strict_types=1);

namespace Kununu\Collection;

use Kununu\Collection\Filter\CollectionFilter;
use Kununu\Collection\Filter\CollectionFilters;
use Kununu\Collection\Filter\FilterItemTrait;

trait FilterableCollectionTrait
{
    use CollectionTrait;
    use FilterItemTrait;

    public function filter(CollectionFilter $filter): self|static
    {
        // @phpstan-ignore new.static
        $filteredResult = new static();
        $this->each(
            static fn(mixed $item) => match (self::filterIsSatisfiedByItem($filter, $item)) {
                false => null,
                true  => $filteredResult->add($item),
            }
        );

        return $filteredResult;
    }

    public function filterWith(callable $function, bool $rewind = true): self|static
    {
        // @phpstan-ignore new.static
        $filteredResult = new static();
        $this->each(
            static function(mixed $item, int|string|null $key) use ($function, $filteredResult): void {
                $value = $function($item, $key);
                if (null !== $value) {
                    $filteredResult->add($value);
                }
            }
        );

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
        return new CollectionFilters(...$filters)->getGroupsForCollection($this, $removeEmptyGroups);
    }
}
