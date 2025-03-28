<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use InvalidArgumentException;
use Kununu\Collection\AbstractCollection;

final class DTOCollectionStub extends AbstractCollection
{
    private const INVALID = 'Can only append array or %s';

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

    public function append($value): void
    {
        match (true) {
            is_array($value)          => $this->append(DTOStub::fromArray($value)),
            $value instanceof DTOStub => $this->offsetSet($value->field, $value),
            default                   => throw new InvalidArgumentException(sprintf(self::INVALID, DTOStub::class)),
        };
    }
}
