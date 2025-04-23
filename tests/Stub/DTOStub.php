<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\Convertible\ToArray;

final readonly class DTOStub implements ToArray
{
    public function __construct(public string $field, public mixed $value)
    {
    }

    public static function fromArray(array $data): self
    {
        return new self($data['field'], $data['value']);
    }

    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'value' => $this->value,
        ];
    }
}
