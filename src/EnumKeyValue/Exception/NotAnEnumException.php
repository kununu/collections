<?php
declare(strict_types=1);

namespace Kununu\Collection\EnumKeyValue\Exception;

use InvalidArgumentException;

final class NotAnEnumException extends InvalidArgumentException
{
    public function __construct(string $class)
    {
        parent::__construct(sprintf('"%s" is not an enum', $class), 400);
    }
}
