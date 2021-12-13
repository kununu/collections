<?php
declare(strict_types=1);

namespace Kununu\Collection;

use Kununu\Collection\Convertible\ToArray;
use Kununu\Collection\Convertible\ToInt;
use Kununu\Collection\Convertible\ToString;

abstract class AbstractItemToArray extends AbstractItem implements ToArray
{
    public function toArray(): array
    {
        $result = [];
        foreach (static::PROPERTIES as $property) {
            $element = $this->getAttribute($property);
            switch (true) {
                case $element instanceof ToArray:
                    $value = $element->toArray();
                    break;
                case $element instanceof ToString:
                    $value = $element->toString();
                    break;
                case $element instanceof ToInt:
                    $value = $element->toInt();
                    break;
                default:
                    $value = $element;
            }
            $result[$property] = $value;
        }

        return $result;
    }
}
