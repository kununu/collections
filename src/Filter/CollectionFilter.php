<?php declare(strict_types=1);

namespace Kununu\Collection\Filter;

interface CollectionFilter
{
    public function key(): string;

    public function isSatisfiedBy(FilterItem $item): bool;

    public function customGroupByData(): ?array;

    public function setCustomGroupByData(?array $customGroupByData = null): CollectionFilter;
}
