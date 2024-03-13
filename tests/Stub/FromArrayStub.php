<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\Convertible\FromArray;

final class FromArrayStub implements FromArray
{
    public function __construct(public readonly int $id, public readonly string $name)
    {
    }

    public static function fromArray(array $data): self
    {
        return new self((int) $data['id'], (string) $data['name']);
    }
}
