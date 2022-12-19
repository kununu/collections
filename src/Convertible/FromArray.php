<?php
declare(strict_types=1);

namespace Kununu\Collection\Convertible;

interface FromArray
{
    public static function fromArray(array $data): self;
}
