<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\AbstractFilterableCollection;
use Kununu\Collection\Collection;
use Kununu\Collection\Filter\CollectionFilter;

/**
 * @method static self      fromIterable(iterable $data)
 * @method        self      add(mixed $value)
 * @method        self      clear()
 * @method        self|null collectionOrNull()
 * @method        int       count()
 * @method        self      diff(Collection $other)
 * @method        self      duplicates(bool $strict = true, bool $uniques = false)
 * @method        self      each(callable $function, bool $rewind = true)
 * @method        self      filter(CollectionFilter $filter)
 * @method        self      filterWith(callable $function, bool $rewind = true)
 * @method        self      intersect(Collection $other)
 * @method        self      merge(Collection ...$others)
 * @method        self      reverse()
 * @method        self      unique()
 */
final class FilterableCollectionStub extends AbstractFilterableCollection
{
    public function __construct(FilterItemStub ...$filterItemStubs)
    {
        parent::__construct();

        foreach ($filterItemStubs as $filterItemStub) {
            $this->append($filterItemStub);
        }
    }
}
