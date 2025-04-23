<?php
declare(strict_types=1);

namespace Kununu\Collection\Filter;

final class FilterOperatorXor extends AbstractFilterOperator
{
    public function __construct()
    {
        parent::__construct(static fn(bool $a, bool $b): bool => $a xor $b, false, true);
    }
}
