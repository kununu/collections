<?php
declare(strict_types=1);

namespace Kununu\Collection\EnumKeyValue;

use ReflectionAttribute;
use ReflectionClassConstant;

trait EnumKeyRequiredTrait
{
    public function required(): bool
    {
        $attributes = array_map(
            static fn(ReflectionAttribute $attr): object => $attr->newInstance(),
            new ReflectionClassConstant(self::class, $this->name)->getAttributes(Required::class)
        );

        return current($attributes) instanceof Required;
    }
}
