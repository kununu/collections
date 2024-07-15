<?php
declare(strict_types=1);

namespace Kununu\Collection\Convertible;

interface FromIterable
{
    public static function fromIterable(iterable $data): self|static;
}
