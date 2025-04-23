<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Filter;

use Kununu\Collection\Filter\FilterOperator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

abstract class AbstractFilterOperatorTestCase extends TestCase
{
    private FilterOperator $filterOperator;

    #[DataProvider('operatorDataProvider')]
    #[TestDox('Testing $_dataName')]
    public function testOperator(bool $operand1, bool $operand2, bool $expected): void
    {
        self::assertEquals($expected, $this->filterOperator->calculate($operand1, $operand2));
    }

    abstract public static function operatorDataProvider(): array;

    public function testInitialValue(): void
    {
        self::assertEquals($this->getExpectedInitialValue(), $this->filterOperator->initialValue());
    }

    public function testExitConditionValue(): void
    {
        self::assertEquals($this->getExpectedExitConditionValue(), $this->filterOperator->exitConditionValue());
    }

    abstract protected function getFilterOperator(): FilterOperator;

    abstract protected function getExpectedInitialValue(): bool;

    abstract protected function getExpectedExitConditionValue(): bool;

    protected function setUp(): void
    {
        $this->filterOperator = $this->getFilterOperator();
    }
}
