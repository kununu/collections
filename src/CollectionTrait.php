<?php
declare(strict_types=1);

namespace Kununu\Collection;

use InvalidArgumentException;
use Kununu\Collection\Convertible\ToArray;
use Kununu\Collection\Convertible\ToInt;
use Kununu\Collection\Convertible\ToString;
use Stringable;

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

    public function toArray(): array
    {
        return $this->mapToArray();
    }

    public function add($value): self|static
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
            static fn(array $chunk): self|static => self::fromIterable($chunk),
            array_chunk($this->getArrayCopy(), $size)
        );
    }

    public function clear(): self|static
    {
        $this->rewind();

        foreach ($this->keys() as $key) {
            $this->offsetUnset($key);
        }

        return $this;
    }

    public function diff(Collection $other): self|static
    {
        if (!$other instanceof static) {
            throw new InvalidArgumentException('Other collection must be of the same type');
        }

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

    public function duplicates(bool $strict = true, bool $uniques = false): self|static
    {
        $elements = new static();
        $duplicates = new static();

        foreach ($this as $element) {
            match ($elements->has($element, $strict)) {
                true  => $duplicates->add($element),
                false => $elements->add($element),
            };
        }
        $this->rewind();

        return $uniques ? $duplicates->unique() : $duplicates;
    }

    public function each(callable $function, bool $rewind = true): self|static
    {
        try {
            foreach ($this as $element) {
                $function($element, $this->key());
            }
        } finally {
            if ($rewind) {
                $this->rewind();
            }
        }

        return $this;
    }

    public function eachChunk(int $size, callable $function): self|static
    {
        foreach ($this->chunk($size) as $chunk) {
            $function($chunk);
        }

        return $this;
    }

    public function empty(): bool
    {
        return 0 === $this->count();
    }

    public function has(mixed $value, bool $strict = true): bool
    {
        return in_array($value, $this->toArray(), $strict);
    }

    public function keys(): array
    {
        return array_keys($this->getArrayCopy());
    }

    public function map(callable $function, bool $rewind = true): array
    {
        $map = [];
        try {
            foreach ($this as $element) {
                $map[] = $function($element, $this->key());
            }
        } finally {
            if ($rewind) {
                $this->rewind();
            }
        }

        return $map;
    }

    public function reduce(callable $function, mixed $initial = null, bool $rewind = true): mixed
    {
        try {
            foreach ($this as $element) {
                $initial = $function($initial, $element, $this->key());
            }
        } finally {
            if ($rewind) {
                $this->rewind();
            }
        }

        return $initial;
    }

    public function reverse(): self|static
    {
        return static::fromIterable(array_reverse($this->toArray()));
    }

    public function unique(): self|static
    {
        return static::fromIterable(array_unique($this->toArray(), SORT_REGULAR));
    }

    public function values(): array
    {
        return array_values($this->getArrayCopy());
    }

    protected function mapToArray(bool $withKeys = true): array
    {
        return array_map(
            static fn(mixed $element): mixed => match (true) {
                $element instanceof ToArray    => $element->toArray(),
                $element instanceof ToString   => $element->toString(),
                $element instanceof ToInt      => $element->toInt(),
                $element instanceof Stringable => (string) $element,
                default                        => $element,
            },
            $withKeys ? $this->getArrayCopy() : $this->values()
        );
    }
}
