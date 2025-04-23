<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Filter;

use Kununu\Collection\Filter\FilterOperator;
use Kununu\Collection\Filter\FilterOperatorOr;

final class FilterOperatorOrTest extends AbstractFilterOperatorTestCase
{
    public static function operatorDataProvider(): array
    {
        return [
            'false_or_false' => [
                false,
                false,
                false,
            ],
            'false_or_true' => [
                false,
                true,
                true,
            ],
            'true_or_false' => [
                true,
                false,
                true,
            ],
            'true_or_true' => [
                true,
                true,
                true,
            ],
        ];
    }

    protected function getFilterOperator(): FilterOperator
    {
        return new FilterOperatorOr();
    }

    protected function getExpectedInitialValue(): bool
    {
        return false;
    }

    protected function getExpectedExitConditionValue(): bool
    {
        return true;
    }
}
