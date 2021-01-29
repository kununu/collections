<?php declare(strict_types=1);

namespace Kununu\Collection;

use ArrayIterator;
use Kununu\Collection\Convertible\ToArray;

abstract class AbstractCollection extends ArrayIterator implements ToArray
{
    use CollectionTrait {
        fromIterable as traitFromIterable;
    }

    /**
     * @param iterable $data
     *
     * @return CollectionTrait|static
     */
    public static function fromIterable(iterable $data): self
    {
        return self::traitFromIterable($data);
    }
}
