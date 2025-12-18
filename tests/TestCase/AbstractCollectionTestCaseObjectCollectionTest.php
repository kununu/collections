<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\TestCase;

use Kununu\Collection\Collection;
use Kununu\Collection\TestCase\AbstractCollectionTestCase;
use Kununu\Collection\Tests\Stub\DTOCollectionStub;
use Kununu\Collection\Tests\Stub\DTOStub;

final class AbstractCollectionTestCaseObjectCollectionTest extends AbstractCollectionTestCase
{
    protected const int EXPECTED_COUNT = 3;
    protected const string EXPECTED_ITEM_CLASS = DTOStub::class;
    protected const string INVALID_ERROR_MESSAGE = 'Can only append array or Kununu\Collection\Tests\Stub\DTOStub';

    protected function createCollection(): DTOCollectionStub
    {
        return (new DTOCollectionStub(new DTOStub('field_3', 3000), new DTOStub('field_1', 'value 1')))
            ->add(['field' => 'field_1', 'value' => 1000])
            ->add(new DTOStub('field_2', 2000))
            ->add(new DTOStub('field_2', 2000));
    }

    protected function createEmptyCollection(): DTOCollectionStub
    {
        return new DTOCollectionStub();
    }

    protected function doExtraAssertions(Collection $collection): void
    {
        self::assertInstanceOf(DTOCollectionStub::class, $collection);
    }

    protected function getExpectedToArray(): array
    {
        return [
            'field_1' => ['field' => 'field_1', 'value' => 1000],
            'field_2' => ['field' => 'field_2', 'value' => 2000],
            'field_3' => ['field' => 'field_3', 'value' => 3000],
        ];
    }
}
