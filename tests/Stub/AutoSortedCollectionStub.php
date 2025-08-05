<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\AbstractCollection;
use Kununu\Collection\AutoSortableOffsetSetTrait;
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
final class AutoSortedCollectionStub extends AbstractCollection
{
    use AutoSortableOffsetSetTrait;

    public function append($value): void
    {
        match (true) {
            is_string($value),
            is_int($value) => $this->offsetSet($value, $value),
            default        => parent::append($value),
        };
    }

    public function toArray(): array
    {
        return $this->mapToArray(false);
    }
}
