<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Filter;

use Kununu\Collection\Filter\FilterOperatorXor;
use PHPUnit\Framework\TestCase;

final class FilterOperatorXOrTest extends TestCase
{
    public function testOperator(): void
    {
        $operator = new FilterOperatorXor();

        $this->assertFalse($operator->initialValue());
        $this->assertTrue($operator->exitConditionValue());
        $this->assertFalse($operator->calculate(false, false));
        $this->assertFalse($operator->calculate(true, true));
        $this->assertTrue($operator->calculate(true, false));
        $this->assertTrue($operator->calculate(false, true));
    }
}
