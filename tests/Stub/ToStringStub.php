<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\Convertible\ToString;

final class ToStringStub implements ToString
{
    private function __construct(private readonly ToIntStub $id, private readonly string $value)
    {
    }

    public static function create(ToIntStub $id, string $value): self
    {
        return new self($id, $value);
    }

    public function toString(): string
    {
        return sprintf('%d: %s', $this->id->toInt(), $this->value);
    }
}
