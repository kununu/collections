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
        $groups = array_merge(...$this->map(static fn(CollectionFilter $filter): array => [$filter->key() => []]));

        $collection->each(function(mixed $item) use (&$groups): void {
            $groups = $this->updateGroupsForItem($item, $groups);
        });

        return $removeEmptyGroups ? array_filter($groups) : $groups;
    }

    private function updateGroupsForItem(mixed $item, array $groups): array
    {
        $this->each(
            static function(CollectionFilter $filter) use (&$groups, $item): void {
                if (self::filterIsSatisfiedByItem($filter, $item)) {
                    $groups[$filter->key()][$item->groupByKey($filter->customGroupByData())] = $item;
                }
            }
        );

        return $groups;
    }
}
