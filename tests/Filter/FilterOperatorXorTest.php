<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Filter;

use Kununu\Collection\Filter\FilterOperator;
use Kununu\Collection\Filter\FilterOperatorXor;

final class FilterOperatorXorTest extends AbstractFilterOperatorTestCase
{
    public static function operatorDataProvider(): array
    {
        return [
            'false_xor_false' => [
                false,
                false,
                false,
            ],
            'false_xor_true'  => [
                false,
                true,
                true,
            ],
            'true_xor_false'  => [
                true,
                false,
                true,
            ],
            'true_xor_true'   => [
                true,
                true,
                false,
            ],
        ];
    }

    protected function getFilterOperator(): FilterOperator
    {
        return new FilterOperatorXor();
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
