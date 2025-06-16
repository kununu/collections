<?php
declare(strict_types=1);

namespace Kununu\Collection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Kununu\Collection\Convertible\FromArray;
use Kununu\Collection\Convertible\FromIterable;
use Kununu\Collection\Convertible\ToArray;

final class KeyValue implements ArrayAccess, Countable, IteratorAggregate, FromArray, FromIterable, ToArray
{
    private array $values;

    public function __construct()
    {
        $this->values = [];
    }

    public static function fromArray(array $data): self
    {
        return self::fromIterable($data);
    }

    public static function fromIterable(iterable $data): self
    {
        $instance = new self();

        foreach ($data as $key => $value) {
            $instance->set($key, $value);
        }

        return $instance;
    }

    public function values(): array
    {
        return array_values($this->values);
    }

    public function keys(): array
    {
        return array_keys($this->values);
    }

    public function get(int|string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->values[$key] : $default;
    }

    public function set(int|string $key, mixed $value): self
    {
        $this->values[$key] = $value;

        return $this;
    }

    public function has(int|string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    public function remove(int|string $key): self
    {
        unset($this->values[$key]);

        return $this;
    }

    public function toArray(): array
    {
        return $this->values;
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
}
