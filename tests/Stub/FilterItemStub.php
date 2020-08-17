<?php declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\Filter\FilterItem;

final class FilterItemStub implements FilterItem
{
    private $itemKey;
    private $extra;

    public function __construct(string $itemKey, ?string $extra = null)
    {
        $this->itemKey = $itemKey;
        $this->extra = $extra;
    }

    public function groupByKey(?array $customData = null): string
    {
        return is_array($customData) && (bool) ($customData['reverseKey'] ?? false)
            ? strrev($this->itemKey)
            : $this->itemKey;
    }

    public function extra(): ?string
    {
        return $this->extra;
    }
}
