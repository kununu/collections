<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Filter;

use Kununu\Collection\Filter\FilterOperatorAnd;
use PHPUnit\Framework\TestCase;

final class FilterOperatorAndTest extends TestCase
{
    public function testOperator(): void
    {
        $operator = new FilterOperatorAnd();

        self::assertTrue($operator->initialValue());
        self::assertFalse($operator->exitConditionValue());
        self::assertTrue($operator->calculate(true, true));
        self::assertFalse($operator->calculate(false, false));
        self::assertFalse($operator->calculate(true, false));
        self::assertFalse($operator->calculate(false, true));
    }
}
