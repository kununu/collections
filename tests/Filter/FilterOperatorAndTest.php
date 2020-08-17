<?php declare(strict_types=1);

namespace Kununu\Collection\Tests\Filter;

use Kununu\Collection\Filter\FilterOperatorAnd;
use PHPUnit\Framework\TestCase;

final class FilterOperatorAndTest extends TestCase
{
    public function testOperator(): void
    {
        $operator = new FilterOperatorAnd();

        $this->assertTrue($operator->initialValue());
        $this->assertFalse($operator->exitConditionValue());
        $this->assertTrue($operator->calculate(true, true));
        $this->assertFalse($operator->calculate(false, false));
        $this->assertFalse($operator->calculate(true, false));
        $this->assertFalse($operator->calculate(false, true));
    }
}
