<?php
declare(strict_types=1);

namespace Kununu\Collection\Filter;

final readonly class FilterOperatorAnd extends AbstractFilterOperator
{
    public function __construct()
    {
        parent::__construct(static fn(bool $a, bool $b): bool => $a && $b, true, false);
    }
}
