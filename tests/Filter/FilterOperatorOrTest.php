<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Filter;

use Kununu\Collection\Filter\FilterOperatorOr;
use PHPUnit\Framework\TestCase;

final class FilterOperatorOrTest extends TestCase
{
    public function testOperator(): void
    {
        $operator = new FilterOperatorOr();

        self::assertFalse($operator->initialValue());
        self::assertTrue($operator->exitConditionValue());
        self::assertFalse($operator->calculate(false, false));
        self::assertTrue($operator->calculate(true, true));
        self::assertTrue($operator->calculate(true, false));
        self::assertTrue($operator->calculate(false, true));
    }
}
