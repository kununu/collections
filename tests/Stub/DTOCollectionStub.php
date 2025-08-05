<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use InvalidArgumentException;
use Kununu\Collection\AbstractCollection;
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
final class DTOCollectionStub extends AbstractCollection
{
    private const string INVALID = 'Can only append array or %s';

    public function __construct(DTOStub ...$dtoStubs)
    {
        parent::__construct();
        foreach ($dtoStubs as $dtoStub) {
            $this->append($dtoStub);
        }
    }

    public function current(): ?DTOStub
    {
        $current = parent::current();
        assert($this->count() > 0 ? $current instanceof DTOStub : null === $current);

        return $current;
    }

    public function append(mixed $value): void
    {
        match (true) {
            is_array($value)          => $this->append(DTOStub::fromArray($value)),
            $value instanceof DTOStub => $this->offsetSet($value->field, $value),
            default                   => throw new InvalidArgumentException(sprintf(self::INVALID, DTOStub::class)),
        };
    }
}
