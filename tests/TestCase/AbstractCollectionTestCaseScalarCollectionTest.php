<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\TestCase;

use Kununu\Collection\Collection;
use Kununu\Collection\TestCase\AbstractCollectionTestCase;
use Kununu\Collection\Tests\Stub\ScalarCollectionStub;
use Kununu\Collection\Tests\Stub\ToIntStub;

final class AbstractCollectionTestCaseScalarCollectionTest extends AbstractCollectionTestCase
{
    protected const int EXPECTED_COUNT = 4;
    protected const bool EXPECTED_ITEM_IS_OBJECT = false;
    protected const mixed INVALID_VALUE = 'i-am-a-happy-not-integer-string';
    protected const string INVALID_ERROR_MESSAGE = 'Can only int or Kununu\Collection\Convertible\ToInt';

    protected function createCollection(): ScalarCollectionStub
    {
        return (new ScalarCollectionStub(3))
            ->add(ToIntStub::fromInt(1))
            ->add(2)
            ->add(ToIntStub::fromInt(1))
            ->add(ToIntStub::fromInt(4));
    }

    protected function createEmptyCollection(): ScalarCollectionStub
    {
        return new ScalarCollectionStub();
    }

    protected function doExtraAssertionForScalarCurrent(mixed $current): void
    {
        self::assertIsInt($current);
    }

    protected function doExtraAssertions(Collection $collection): void
    {
        self::assertInstanceOf(ScalarCollectionStub::class, $collection);
    }

    protected function getExpectedToArray(): array
    {
        return [1, 2, 3, 4];
    }
}
