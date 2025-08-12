<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\Filter\FilterItem;

final readonly class FilterItemStub implements FilterItem
{
    public function __construct(public string $itemKey, private ?string $extra = null)
    {
    }

    public function groupByKey(?array $customData = null): string
    {
        return is_array($customData) && ($customData['reverseKey'] ?? false)
            ? strrev($this->itemKey)
            : $this->itemKey;
    }

    public function extra(): ?string
    {
        return $this->extra;
    }
}
