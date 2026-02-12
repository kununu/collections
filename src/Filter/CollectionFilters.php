<?php
declare(strict_types=1);

namespace Kununu\Collection\Filter;

use InvalidArgumentException;
use Kununu\Collection\AbstractCollection;
use Kununu\Collection\Collection;

/**
 * @method static self fromIterable(iterable $data)
 * @method        self add(mixed $value)
 * @method        self clear()
 * @method        int  count()
 * @method        self diff(Collection $other)
 * @method        self duplicates(bool $strict = true, bool $uniques = false)
 * @method        self each(callable $function, bool $rewind = true)
 * @method        self reverse()
 * @method        self unique()
 */
final class CollectionFilters extends AbstractCollection
{
    use FilterItemTrait;

    private const string INVALID = 'Can only append %s or another instance of %s';

    private ?array $groups = null;

    public function __construct(CollectionFilter ...$filters)
    {
        if (count($filters)) {
            parent::__construct($filters);
        }
    }

    public function current(): ?CollectionFilter
    {
        $current = parent::current();
        assert($this->count() > 0 ? $current instanceof CollectionFilter : null === $current);

        return $current;
    }

    /** @throws InvalidArgumentException */
    public function append(mixed $value): void
    {
        match (true) {
            $value instanceof CollectionFilter => parent::append($value),
            $value instanceof self             => $value->each(fn(CollectionFilter $filter) => parent::append($filter)),
            default                            => throw new InvalidArgumentException(
                sprintf(self::INVALID, CollectionFilter::class, self::class)
            ),
        };
    }

    public function getGroupsForCollection(Collection $collection, bool $removeEmptyGroups): array
    {
        $this->groups = $this->reduce(
            static function(array $group, CollectionFilter $filter): array {
                $group[$filter->key()] = [];

                return $group;
            },
            []
        );

        $collection->each(fn(mixed $item) => $this->updateGroupsForItem($item));

        $groups = $removeEmptyGroups ? array_filter($this->groups) : $this->groups;
        $this->groups = null;

        return $groups;
    }

    private function updateGroupsForItem(mixed $item): self
    {
        $this->each(
            fn(CollectionFilter $filter) => match (self::filterIsSatisfiedByItem($filter, $item)) {
                false => null,
                true  => $this->groups[$filter->key()][$item->groupByKey($filter->customGroupByData())] = $item,
            }
        );

        return $this;
    }
}
