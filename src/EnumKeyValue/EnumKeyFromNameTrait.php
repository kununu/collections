<?php
declare(strict_types=1);

namespace Kununu\Collection\EnumKeyValue;

use InvalidArgumentException;

trait EnumKeyFromNameTrait
{
    public static function fromName(string $name): self
    {
        $enum = array_column(self::cases(), null, 'name')[$name] ?? null;

        return match (true) {
            $enum === null => throw new InvalidArgumentException(
                sprintf('"%s" is not a valid case for enum "%s"', $name, self::class)
            ),
            default => $enum,
        };
    }
}
