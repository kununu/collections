<?php
declare(strict_types=1);

namespace Kununu\Collection;

use Kununu\Collection\Convertible\ToArray;
use Kununu\Collection\Convertible\ToInt;
use Kununu\Collection\Convertible\ToString;
use Throwable;

trait CollectionTrait
{
    public static function fromIterable(iterable $data): self|static
    {
        $result = new static();

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
        return static::fromIterable(array_unique($this->toArray(), SORT_REGULAR));
    }

    public function reverse(): self
    {
        return static::fromIterable(array_reverse($this->toArray()));
    }

    public function diff(self $other): self
    {
        return static::fromIterable(
            array_values(
                array_map(
                    'unserialize',
                    array_diff(
                        array_map('serialize', $this->toArray()),
                        array_map('serialize', $other->toArray())
                    )
                )
            )
        );
    }

    public function each(callable $function, bool $rewind = true): self
    {
        try {
            foreach ($this as $element) {
                $function($element, $this->key());
            }
        } catch (Throwable $e) {
            throw $e;
        } finally {
            if ($rewind) {
                $this->rewind();
            }
        }

        return $this;
    }

    public function map(callable $function, bool $rewind = true): array
    {
        $map = [];
        try {
            foreach ($this as $element) {
                $map[] = $function($element, $this->key());
            }
        } catch (Throwable $e) {
            throw $e;
        } finally {
            if ($rewind) {
                $this->rewind();
            }
        }

        return $map;
    }

    public function reduce(callable $function, $initial = null, bool $rewind = true)
    {
        try {
            foreach ($this as $element) {
                $initial = $function($initial, $element, $this->key());
            }
        } catch (Throwable $e) {
            throw $e;
        } finally {
            if ($rewind) {
                $this->rewind();
            }
        }

        return $initial;
    }

    public function toArray(): array
    {
        return $this->mapToArray();
    }

    protected function mapToArray(bool $withKeys = true): array
    {
        return array_map(
            fn($element) => match (true) {
                $element instanceof ToArray  => $element->toArray(),
                $element instanceof ToString => $element->toString(),
                $element instanceof ToInt    => $element->toInt(),
                default                      => $element,
            },
            $withKeys ? $this->getArrayCopy() : array_values($this->getArrayCopy())
        );
    }
}
