<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\Convertible\ToArray;

final class DTOStub implements ToArray
{
    private $field;
    private $value;

    public function __construct(string $field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public static function fromArray(array $data): self
    {
        return new self($data['field'], $data['value']);
    }

    public function field(): string
    {
        return $this->field;
    }

    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'value' => $this->value,
        ];
    }
}
