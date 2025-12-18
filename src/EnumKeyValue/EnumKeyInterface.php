<?php
declare(strict_types=1);

namespace Kununu\Collection\EnumKeyValue;

interface EnumKeyInterface
{
    public function key(): string;

    public function required(): bool;
}
