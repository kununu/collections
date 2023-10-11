<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\Convertible\FromArray;

final class FromArrayStub implements FromArray
{
    public function __construct(private int $id, private string $name)
    {
    }

    public static function fromArray(array $data): self
    {
        return new self((int) $data['id'], (string) $data['name']);
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }
}
