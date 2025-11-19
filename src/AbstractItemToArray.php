<?php
declare(strict_types=1);

namespace Kununu\Collection;

use Kununu\Collection\Convertible\ToArray;

abstract class AbstractItemToArray extends AbstractItem implements ToArray
{
    use MapArrayItemsTrait;

    public function toArray(): array
    {
        return array_combine(
            $properties = $this->getAllProperties(),
            $this->mapArrayItems(
                array_map(
                    $this->getAttribute(...),
                    $properties
                )
            )
        );
    }
}
