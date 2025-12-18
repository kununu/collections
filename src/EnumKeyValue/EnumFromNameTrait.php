<?php
declare(strict_types=1);

namespace Kununu\Collection\EnumKeyValue;

use ValueError;

trait EnumFromNameTrait
{
    public static function tryFromName(string $name): ?static
    {
        return array_column(self::cases(), null, 'name')[$name] ?? null;
    }

    public static function fromName(string $name): static
    {
        return self::tryFromName($name)
            ?? throw new ValueError(sprintf('"%s" is not a valid case for enum "%s"', $name, static::class));
    }
}
