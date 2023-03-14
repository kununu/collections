<?php
declare(strict_types=1);

namespace Kununu\Collection\Filter;

abstract class BaseFilter implements CollectionFilter
{
    private ?array $customGroupByData = null;

    public function __construct(private string $key)
    {
    }

    public function key(): string
    {
        return $this->key;
    }

    public function customGroupByData(): ?array
    {
        return $this->customGroupByData;
    }

    public function setCustomGroupByData(?array $customGroupByData = null): CollectionFilter
    {
        $this->customGroupByData = $customGroupByData;

        return $this;
    }
}
