<?php
declare(strict_types=1);

namespace Kununu\Collection\Exception;

use InvalidArgumentException;

final class NotSameCollectionTypeException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Other collection must be of the same type', 400);
    }
}
