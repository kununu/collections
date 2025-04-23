<?php
declare(strict_types=1);

namespace Kununu\Collection\Filter;

use Closure;

abstract class AbstractFilterOperator implements FilterOperator
{
    public function __construct(
        private readonly Closure $calculator,
        private readonly bool $initialValue,
        private readonly bool $exitConditionValue,
    ) {
    }

    public function calculate(bool $operand1, bool $operand2): bool
    {
        return ($this->calculator)($operand1, $operand2);
    }

    public function initialValue(): bool
    {
        return $this->initialValue;
    }

    public function exitConditionValue(): bool
    {
        return $this->exitConditionValue;
    }
}
