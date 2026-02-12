<?php
declare(strict_types=1);

namespace Kununu\Collection\Convertible;

use Stringable;

interface FromStringable
{
    public static function fromStringable(string|Stringable $value): self|static;
}
