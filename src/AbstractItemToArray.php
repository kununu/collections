<?php
declare(strict_types=1);

namespace Kununu\Collection;

use Kununu\Collection\Convertible\ToArray;
use Kununu\Collection\Convertible\ToInt;
use Kununu\Collection\Convertible\ToString;
use Stringable;

abstract class AbstractItemToArray extends AbstractItem implements ToArray
{
    public function toArray(): array
    {
        $result = [];
        foreach ($this->getAllProperties() as $property) {
            $element = $this->getAttribute($property);
            $result[$property] = match (true) {
                $element instanceof ToArray    => $element->toArray(),
                $element instanceof ToString   => $element->toString(),
                $element instanceof ToInt      => $element->toInt(),
                $element instanceof Stringable => (string) $element,
                default                        => $element,
            };
        }

        return $result;
    }
}
