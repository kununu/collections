<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Stringable;

final readonly class StringableStub implements Stringable
{
    private function __construct(private ToIntStub $id, private string $value)
    {
    }

    public static function create(ToIntStub $id, string $value): self
    {
        return new self($id, $value);
    }

    public function __toString(): string
    {
        return sprintf('%d: %s', $this->id->toInt(), $this->value);
    }
}
