<?php declare(strict_types=1);

namespace Kununu\Collection;

use Kununu\Collection\Convertible\ToArray;
use Kununu\Collection\Convertible\ToInt;
use Kununu\Collection\Convertible\ToString;

trait CollectionTrait
{
    public static function fromIterable(iterable $data): self
    {
        $result = new self();

        foreach ($data as $item) {
            $result->append($item);
        }

        return $result;
    }

    public function empty(): bool
    {
        return 0 === $this->count();
    }

    public function add($value): self
    {
        $this->append($value);

        return $this;
    }

    public function unique(): self
    {
        return self::fromIterable(array_unique($this->toArray(), SORT_REGULAR));
    }

    public function reverse(): self
    {
        return self::fromIterable(array_reverse($this->toArray()));
    }

    public function diff(self $other): self
    {
        return self::fromIterable(array_values(array_map(
            'unserialize',
            array_diff(
                array_map('serialize', $this->toArray()),
                array_map('serialize', $other->toArray())
            )
        )));
    }

    public function toArray(): array
    {
        return $this->mapToArray();
    }

    private function mapToArray(bool $withKeys = true): array
    {
        return array_map(
            function($element) {
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

                return $value;
            },
            $withKeys ? $this->getArrayCopy() : array_values($this->getArrayCopy())
        );
    }
}
