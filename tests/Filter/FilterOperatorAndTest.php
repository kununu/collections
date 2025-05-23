<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Filter;

use Kununu\Collection\Filter\FilterOperator;
use Kununu\Collection\Filter\FilterOperatorAnd;

final class FilterOperatorAndTest extends AbstractFilterOperatorTestCase
{
    public static function operatorDataProvider(): array
    {
        return [
            'false_and_false' => [
                false,
                false,
                false,
            ],
            'false_and_true' => [
                false,
                true,
                false,
            ],
            'true_and_false' => [
                true,
                false,
                false,
            ],
            'true_and_true' => [
                true,
                true,
                true,
            ],
        ];
    }

    protected function getFilterOperator(): FilterOperator
    {
        return new FilterOperatorAnd();
    }

    protected function getExpectedInitialValue(): bool
    {
        return true;
    }

    protected function getExpectedExitConditionValue(): bool
    {
        return false;
    }
}
