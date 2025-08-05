<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\AbstractFilterableCollection;
use Kununu\Collection\Collection;
use Kununu\Collection\Filter\CollectionFilter;

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
 * @method        self filter(CollectionFilter $filter)
 */
final class FilterableCollectionStub extends AbstractFilterableCollection
{
}
