<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use InvalidArgumentException;
use Kununu\Collection\AbstractCollection;
use Kununu\Collection\AutoSortableOffsetSetTrait;
use Kununu\Collection\Collection;
use Kununu\Collection\Convertible\ToInt;

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
final class ScalarCollectionStub extends AbstractCollection
{
    use AutoSortableOffsetSetTrait;

    private const string INVALID = 'Can only int or %s';

    public function __construct(int ...$values)
    {
        parent::__construct();
        foreach ($values as $value) {
            $this->append($value);
        }
    }

    public function current(): ?int
    {
        $current = parent::current();
        assert($this->count() > 0 ? is_int($current) : null === $current);

        return $current;
    }

    public function append(mixed $value): void
    {
        match (true) {
            is_int($value)          => $this->offsetSet($value, $value),
            $value instanceof ToInt => $this->append($value->toInt()),
            default                 => throw new InvalidArgumentException(sprintf(self::INVALID, ToInt::class)),
        };
    }

    public function toArray(): array
    {
        return $this->mapToArray(false);
    }
}
