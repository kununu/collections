<?php
declare(strict_types=1);

namespace Kununu\Collection;

use InvalidArgumentException;

trait CollectionTrait
{
    use MapArrayItemsTrait;

    public static function fromIterable(iterable $data): self|static
    {
        // @phpstan-ignore new.static
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

    public function add(mixed $value): self|static
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
        return $this->doWithRewind(
            function(Collection $elements, Collection $duplicates, bool $strict, bool $uniques): Collection {
                foreach ($this as $element) {
                    match ($elements->has($element, $strict)) {
                        true  => $duplicates->add($element),
                        false => $elements->add($element),
                    };
                }

                return $uniques ? $duplicates->unique() : $duplicates;
            },
            true,
            // @phpstan-ignore new.static
            new static(),
            // @phpstan-ignore new.static
            new static(),
            $strict,
            $uniques
        );
    }

    public function each(callable $function, bool $rewind = true): self|static
    {
        $this->doWithRewind(
            function() use ($function): void {
                foreach ($this as $element) {
                    $function($element, $this->key());
                }
            },
            $rewind
        );

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
        return $this->count() === 0;
    }

    public function has(mixed $value, bool $strict = true): bool
    {
        return in_array($value, $this->toArray(), $strict);
    }

    public function hasMultipleItems(): bool
    {
        return $this->count() > 1;
    }

    public function keys(): array
    {
        return array_keys($this->getArrayCopy());
    }

    public function map(callable $function, bool $rewind = true): array
    {
        return $this->doWithRewind(
            function(array $map) use ($function): array {
                foreach ($this as $element) {
                    $map[] = $function($element, $this->key());
                }

                return $map;
            },
            $rewind,
            []
        );
    }

    public function reduce(callable $function, mixed $initial = null, bool $rewind = true): mixed
    {
        return $this->doWithRewind(
            function(mixed $initial, callable $function): mixed {
                foreach ($this as $element) {
                    $initial = $function($initial, $element, $this->key());
                }

                return $initial;
            },
            $rewind,
            $initial,
            $function
        );
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
        return $this->mapArrayItems($withKeys ? $this->getArrayCopy() : $this->values());
    }

    protected function doWithRewind(callable $fn, bool $rewind, mixed ...$arguments): mixed
    {
        try {
            return $fn(...$arguments);
        } finally {
            if ($rewind) {
                $this->rewind();
            }
        }
    }
}
