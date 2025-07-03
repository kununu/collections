<?php
declare(strict_types=1);

namespace Kununu\Collection;

use Kununu\Collection\Convertible\ToArray;
use Kununu\Collection\Convertible\ToInt;
use Kununu\Collection\Convertible\ToString;
use Stringable;

trait MapArrayItemsTrait
{
    protected function mapArrayItems(array $data): array
    {
        return array_map(
            static fn(mixed $item): mixed => match (true) {
                $item instanceof ToArray    => $item->toArray(),
                $item instanceof ToString   => $item->toString(),
                $item instanceof ToInt      => $item->toInt(),
                $item instanceof Stringable => (string) $item,
                default                     => $item,
            },
            $data
        );
    }
}
