<?php
declare(strict_types=1);

namespace Kununu\Collection\Convertible;

interface FromInt
{
    public static function fromInt(int $value): self|static;
}
