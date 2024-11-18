<?php
declare(strict_types=1);

namespace Kununu\Collection;

use Kununu\Collection\Convertible\ToArray;
use Kununu\Collection\Convertible\ToInt;
use Kununu\Collection\Convertible\ToString;
use Throwable;

trait CollectionTrait
{
    public static function fromIterable(iterable $data): self
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

    /** @return self[] */
    public function chunk(int $size): array
    {
        if ($size < 1) {
            return [$this];
        }

        return array_map(
            static function(array $chunk) {
                return self::fromIterable($chunk);
            },
            array_chunk($this->getArrayCopy(), $size)
        );
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

    public function eachChunk(int $size, callable $function): self
    {
        foreach ($this->chunk($size) as $chunk) {
            $function($chunk);
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
