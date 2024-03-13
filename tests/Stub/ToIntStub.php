<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\Convertible\ToInt;

final class ToIntStub implements ToInt
{
    private function __construct(private readonly int $value)
    {
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public function toInt(): int
    {
        return $this->value;
    }
}
