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
 * @method self filter(CollectionFilter $filter)
 */
abstract class AbstractFilterableCollection extends ArrayIterator implements ToArray
{
    use FilterableCollectionTrait;
}
