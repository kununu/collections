<?php
declare(strict_types=1);

namespace Kununu\Collection;

use Kununu\Collection\Convertible\FromIterable;
use Kununu\Collection\Convertible\ToArray;

interface Collection extends FromIterable, ToArray
{
    public function add(mixed $value): self|static;

    /** @return self[]|static[] */
    public function chunk(int $size): array;

    public function diff(self $other): self|static;

    public function duplicates(bool $strict = true, bool $uniques = false): self|static;

    public function each(callable $function, bool $rewind = true): self|static;

    public function eachChunk(int $size, callable $function): self|static;

    public function empty(): bool;

    public function has(mixed $value, bool $strict = true): bool;

    public function keys(): array;

    public function map(callable $function, bool $rewind = true): array;

    public function reduce(callable $function, mixed $initial = null, bool $rewind = true): mixed;

    public function reverse(): self|static;

    public function unique(): self|static;

    public function values(): array;
}
