<?php declare(strict_types=1);

namespace Kununu\Collection\Tests\Filter;

use Kununu\Collection\Filter\FilterOperatorOr;
use PHPUnit\Framework\TestCase;

final class FilterOperatorOrTest extends TestCase
{
    public function testOperator(): void
    {
        $operator = new FilterOperatorOr();

        $this->assertFalse($operator->initialValue());
        $this->assertTrue($operator->exitConditionValue());
        $this->assertFalse($operator->calculate(false, false));
        $this->assertTrue($operator->calculate(true, true));
        $this->assertTrue($operator->calculate(true, false));
        $this->assertTrue($operator->calculate(false, true));
    }
}
