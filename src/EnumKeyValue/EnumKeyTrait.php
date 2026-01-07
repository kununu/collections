<?php
declare(strict_types=1);

namespace Kununu\Collection\EnumKeyValue;

use BackedEnum;
use Kununu\Collection\EnumKeyValue\Exception\NotAnEnumException;
use UnitEnum;

trait EnumKeyTrait
{
    public function key(): string
    {
        return match (true) {
            $this instanceof BackedEnum && is_string($this->value) => $this->value,
            $this instanceof UnitEnum                              => $this->name,
            default                                                => throw new NotAnEnumException($this::class),
        };
    }
}
