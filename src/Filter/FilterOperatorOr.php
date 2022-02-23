<?php
declare(strict_types=1);

namespace Kununu\Collection\Filter;

final class FilterOperatorOr implements FilterOperator
{
    public function calculate(bool $operand1, bool $operand2): bool
    {
        return $operand1 || $operand2;
    }

    public function initialValue(): bool
    {
        return false;
    }

    public function exitConditionValue(): bool
    {
        return true;
    }
}
