<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\Convertible\ToArray;

final readonly class ToArrayStub implements ToArray
{
    private function __construct(private ToIntStub $id, private ToStringStub $data)
    {
    }

    public static function create(ToIntStub $id, ToStringStub $data): self
    {
        return new self($id, $data);
    }

    public function toArray(): array
    {
        return [
            'id'   => $this->id->toInt(),
            'data' => $this->data->toString(),
        ];
    }
}
