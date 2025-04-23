<?php
declare(strict_types=1);

namespace Kununu\Collection;

use ArrayIterator;

/**
 * @method static self fromIterable(iterable $data)
 * @method        self add(mixed $value)
 * @method        self clear()
 * @method        self diff(Collection $other)
 * @method        self duplicates(bool $strict = true, bool $uniques = false)
 * @method        self each(callable $function, bool $rewind = true)
 * @method        self reverse()
 * @method        self unique()
 */
abstract class AbstractCollection extends ArrayIterator implements Collection
{
    use CollectionTrait;
}
