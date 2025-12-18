<?php
declare(strict_types=1);

namespace Kununu\Collection\EnumKeyValue\Exception;

use InvalidArgumentException;
use Kununu\Collection\EnumKeyValue\EnumKeyInterface;

final class RemovingRequiredKeyException extends InvalidArgumentException
{
    public function __construct(EnumKeyInterface $key)
    {
        parent::__construct(sprintf('Removing required key: "%s"', $key->key()), 400);
    }
}
