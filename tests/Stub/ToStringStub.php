<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\Convertible\ToString;

final class ToStringStub implements ToString
{
    /** @var ToIntStub */
    private $id;
    /** @var string */
    private $value;

    private function __construct()
    {
    }

    public static function create(ToIntStub $id, string $value): self
    {
        return (new self())
            ->setId($id)
            ->setValue($value);
    }

    public function toString(): string
    {
        return sprintf('%d: %s', $this->id->toInt(), $this->value);
    }

    private function setId(ToIntStub $id): ToStringStub
    {
        $this->id = $id;

        return $this;
    }

    public function setValue(string $value): ToStringStub
    {
        $this->value = $value;

        return $this;
    }
}
