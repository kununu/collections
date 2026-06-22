<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\AbstractCollection;
use Kununu\Collection\Collection;

/**
 * @method static self      fromIterable(iterable $data)
 * @method        self      add(mixed $value)
 * @method        self      clear()
 * @method        self|null collectionOrNull()
 * @method        int       count()
 * @method        self      diff(Collection $other)
 * @method        self      duplicates(bool $strict = true, bool $uniques = false)
 * @method        self      each(callable $function, bool $rewind = true)
 * @method        self      intersect(Collection $other)
 * @method        self      merge(Collection ...$others)
 * @method        self      reverse()
 * @method        self      unique()
 */
final class CollectionStub extends AbstractCollection
{
}
