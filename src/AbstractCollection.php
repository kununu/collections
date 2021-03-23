<?php declare(strict_types=1);

namespace Kununu\Collection;

use ArrayIterator;
use Kununu\Collection\Convertible\ToArray;

/**
 * @method static self fromIterable(iterable $data)
 * @method self add($value)
 * @method self unique()
 * @method self reverse()
 * @method self diff(self $other)
 * @method self each(callable $function, bool $rewind = true)
 * @method array map(callable $function, bool $rewind = true)
 * @method mixed reduce(callable $function, mixed $initial = null, bool $rewind = true)
 */
abstract class AbstractCollection extends ArrayIterator implements ToArray
{
    use CollectionTrait;
}
