<?php
declare(strict_types=1);

namespace Kununu\Collection;

use ArrayIterator;

trait KeyValueTrait
{
    private array $values = [];

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self|static
    {
        return self::fromIterable($data);
    }

    /** @param iterable<string, mixed> $data */
    public static function fromIterable(iterable $data): self|static
    {
        // @phpstan-ignore new.static
        $instance = new static();

        foreach ($data as $key => $value) {
            $instance->set($key, $value);
        }

        return $instance;
    }

    public function count(): int
    {
        return count($this->values);
    }

    /** @return ArrayIterator<int|string, mixed> */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    public function toArray(): array
    {
        return $this->values;
    }

    public function values(): array
    {
        return array_values($this->values);
    }
}
