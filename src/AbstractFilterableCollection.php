<?php
declare(strict_types=1);

namespace Kununu\Collection;

use ArrayIterator;
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
abstract class AbstractFilterableCollection extends ArrayIterator implements FilterableCollection
{
    use FilterableCollectionTrait;
}
