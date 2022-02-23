<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\Convertible\ToArray;

final class ToArrayStub implements ToArray
{
    /** @var ToIntStub */
    public $id;
    /** @var ToStringStub */
    public $data;

    private function __construct()
    {
    }

    public static function create(ToIntStub $id, ToStringStub $data): self
    {
        return (new self())
            ->setId($id)
            ->setData($data);
    }

    public function toArray(): array
    {
        return [
            'id'   => $this->id->toInt(),
            'data' => $this->data->toString(),
        ];
    }

    private function setId(ToIntStub $id): self
    {
        $this->id = $id;

        return $this;
    }

    private function setData(ToStringStub $data): self
    {
        $this->data = $data;

        return $this;
    }
}
