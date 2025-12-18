<?php
declare(strict_types=1);

namespace Kununu\Collection\EnumKeyValue\Exception;

use InvalidArgumentException;
use Kununu\Collection\EnumKeyValue\EnumKeyInterface;

final class RequiredKeyMissingException extends InvalidArgumentException
{
    public function __construct(EnumKeyInterface $key)
    {
        parent::__construct(sprintf('Missing required key: "%s"', $key->key()), 400);
    }
}
