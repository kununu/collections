<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Filter;

use Kununu\Collection\Filter\BaseFilter;
use Kununu\Collection\Filter\CollectionFilter;
use Kununu\Collection\Filter\CollectionFilters;
use Kununu\Collection\Filter\FilterItem;
use Kununu\Collection\TestCase\AbstractCollectionTestCase;

final class CollectionFiltersTest extends AbstractCollectionTestCase
{
    protected const int EXPECTED_COUNT = 4;
    protected const string EXPECTED_ITEM_CLASS = CollectionFilter::class;
    protected const bool TEST_TO_ARRAY = false;

    protected function createCollection(): CollectionFilters
    {
        return new CollectionFilters()
            ->add($this->createFilter('1'))
            ->add($this->createFilter('1'))
            ->add($this->createFilter('2'))
            ->add($this->createFilter('3'));
    }

    protected function createEmptyCollection(): CollectionFilters
    {
        return new CollectionFilters();
    }

    private function createFilter(string $key): CollectionFilter
    {
        return new class($key) extends BaseFilter {
            public function isSatisfiedBy(FilterItem $item): bool
            {
                return $item->groupByKey() === 'a' || $item->groupByKey() === 'c';
            }
        };
    }
}
