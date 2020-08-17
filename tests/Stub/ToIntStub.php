<?php declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\Convertible\ToInt;

final class ToIntStub implements ToInt
{
    private $value;

    private function __construct()
    {
    }

    public static function fromInt(int $value): self
    {
        return (new self())->setValue($value);
    }

    public function toInt(): int
    {
        return $this->value;
    }

    private function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }
}
