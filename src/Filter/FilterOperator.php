<?php
declare(strict_types=1);

namespace Kununu\Collection\Filter;

interface FilterOperator
{
    public function calculate(bool $operand1, bool $operand2): bool;

    public function initialValue(): bool;

    public function exitConditionValue(): bool;
}
