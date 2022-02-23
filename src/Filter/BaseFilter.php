<?php
declare(strict_types=1);

namespace Kununu\Collection\Filter;

abstract class BaseFilter implements CollectionFilter
{
    private $key;
    private $customGroupByData;

    public function __construct(string $key)
    {
        $this->key = $key;
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
