<?php
declare(strict_types=1);

namespace Kununu\Collection\Filter;

interface FilterItem
{
    public function groupByKey(?array $customData = null): string;
}
