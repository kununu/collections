<?php declare(strict_types=1);

namespace Kununu\Collection;

use ArrayIterator;
use Kununu\Collection\Convertible\ToArray;
use Kununu\Collection\Filter\CollectionFilter;

/**
 * @method static self fromIterable(iterable $data)
 * @method self add($value)
 * @method self unique()
 * @method self reverse()
 * @method self diff(self $other)
 * @method self each(callable $function, bool $rewind = true)
 * @method array map(callable $function, bool $rewind = true)
 * @method mixed reduce(callable $function, mixed $initial = null, bool $rewind = true)
 * @method self filter(CollectionFilter $filter)
 */
abstract class AbstractFilterableCollection extends ArrayIterator implements ToArray
{
    use FilterableCollectionTrait;
}
