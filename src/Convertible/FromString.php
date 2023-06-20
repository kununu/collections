<?php
declare(strict_types=1);

namespace Kununu\Collection\Convertible;

interface FromString
{
    public static function fromString(string $value): self|static;
}
