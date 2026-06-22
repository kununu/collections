<?php
declare(strict_types=1);

namespace Kununu\Collection;

final class KeyValue implements KeyValueInterface
{
    use KeyValueTrait;

    public function get(int|string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->values[$key] : $default;
    }

    public function has(int|string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    public function keys(): array
    {
        return array_keys($this->values);
    }

    public function remove(int|string $key): self
    {
        unset($this->values[$key]);

        return $this;
    }

    public function set(int|string $key, mixed $value): self
    {
        $this->values[$key] = $value;

        return $this;
    }
}
