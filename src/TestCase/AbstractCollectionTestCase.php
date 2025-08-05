<?php
declare(strict_types=1);

namespace Kununu\Collection\TestCase;

use InvalidArgumentException;
use Kununu\Collection\Collection;
use PHPUnit\Framework\TestCase;

abstract class AbstractCollectionTestCase extends TestCase
{
    protected const int EXPECTED_COUNT = 0;
    protected const string EXPECTED_ITEM_CLASS = '';
    protected const bool TEST_TO_ARRAY = true;
    protected const mixed INVALID_VALUE = 42;
    protected const string INVALID_EXCEPTION_CLASS = InvalidArgumentException::class;
    protected const ?string INVALID_ERROR_MESSAGE = null;

    public function testCollection(): void
    {
        $collection = $this->createCollection();

        self::assertCount(static::EXPECTED_COUNT, $collection);
        self::assertInstanceOf(static::EXPECTED_ITEM_CLASS, $collection->current());
        if (static::TEST_TO_ARRAY) {
            self::assertEquals($this->getExpectedToArray(), $collection->toArray());
        }

        $this->doExtraAssertions($collection);
    }

    public function testEmptyCollection(): void
    {
        $collection = $this->createEmptyCollection();

        self::assertEmpty($collection);
        self::assertNull($collection->current());
    }

    public function testAddInvalid(): void
    {
        $this->expectException(static::INVALID_EXCEPTION_CLASS);

        if (static::INVALID_ERROR_MESSAGE !== null) {
            $this->expectExceptionMessage(static::INVALID_ERROR_MESSAGE);
        }

        $this->createEmptyCollection()->add(static::INVALID_VALUE);
    }

    abstract protected function createCollection(): Collection;

    abstract protected function createEmptyCollection(): Collection;

    /** @codeCoverageIgnore */
    protected function getExpectedToArray(): array
    {
        return [];
    }

    /** @codeCoverageIgnore */
    protected function doExtraAssertions(Collection $collection): void
    {
        // Ready to be overridden in your test
    }
}
