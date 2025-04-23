<?php
declare(strict_types=1);

namespace Kununu\Collection\Filter;

final class FilterOperatorOr extends AbstractFilterOperator
{
    public function __construct()
    {
        parent::__construct(static fn(bool $a, bool $b): bool => $a || $b, false, true);
    }
}
