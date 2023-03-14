<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests;

use Kununu\Collection\Filter\BaseFilter;
use Kununu\Collection\Filter\CompositeFilter;
use Kununu\Collection\Filter\FilterItem;
use Kununu\Collection\Filter\FilterOperatorAnd;
use Kununu\Collection\Filter\FilterOperatorOr;
use Kununu\Collection\Tests\Stub\FilterableCollectionStub;
use Kununu\Collection\Tests\Stub\FilterItemStub;
use PHPUnit\Framework\TestCase;

final class FilterableCollectionTest extends TestCase
{
    public function testFilter(): void
    {
        $collection = (new FilterableCollectionStub())
            ->add(1)
            ->add(new FilterItemStub('a'))
            ->add(new FilterItemStub('b'))
            ->add(new FilterItemStub('c'))
            ->add(new FilterItemStub('d'))
            ->add('a string');

        $filter = new class() extends BaseFilter {
            public function __construct()
            {
                parent::__construct('My Filter');
            }

            public function isSatisfiedBy(FilterItem $item): bool
            {
                return $item->groupByKey() === 'a' || $item->groupByKey() === 'c';
            }
        };

        $filteredCollection = $collection->filter($filter);

        $this->assertCount(2, $filteredCollection);
        foreach ($filteredCollection as $item) {
            $this->assertInstanceOf(FilterItemStub::class, $item);
        }
        $this->assertEquals('a', $filteredCollection[0]->groupByKey());
        $this->assertEquals('c', $filteredCollection[1]->groupByKey());

        $collection = (new FilterableCollectionStub())
            ->add(1)
            ->add(new FilterItemStub('b'))
            ->add(new FilterItemStub('d'))
            ->add('a string');

        $this->assertEmpty($collection->filter($filter));
    }

    public function testGroupBy(): void
    {
        $filter1 = new class() extends BaseFilter {
            public function __construct()
            {
                parent::__construct('Filter 1');
            }

            public function isSatisfiedBy(FilterItem $item): bool
            {
                return $item->groupByKey(['reverseKey' => true]) === 'ba';
            }
        };

        $filter1->setCustomGroupByData(['reverseKey' => true]);

        $filter2 = new class() extends BaseFilter {
            public function __construct()
            {
                parent::__construct('Filter 2');
            }

            public function isSatisfiedBy(FilterItem $item): bool
            {
                return $item->groupByKey() === 'b';
            }
        };

        $filter3 = new class() extends BaseFilter {
            public function __construct()
            {
                parent::__construct('Filter 3');
            }

            public function isSatisfiedBy(FilterItem $item): bool
            {
                return $item->groupByKey() === 'c';
            }
        };

        $filter4 = new CompositeFilter('Filter 4', new FilterOperatorAnd(), $filter1, $filter3);
        $filter5 = new CompositeFilter('Filter 5', new FilterOperatorOr(), $filter1, $filter3);

        $filter6 = new class() extends BaseFilter {
            public function __construct()
            {
                parent::__construct('Filter 6');
            }

            public function isSatisfiedBy(FilterItem $item): bool
            {
                return $item->groupByKey() === 'x';
            }
        };

        $collection = (new FilterableCollectionStub())
            ->add(1)
            ->add(new FilterItemStub('ab'))
            ->add(new FilterItemStub('b'))
            ->add(new FilterItemStub('c'))
            ->add(new FilterItemStub('d'))
            ->add('string');

        $groups = $collection->groupBy(false, $filter1, $filter2, $filter3, $filter4, $filter5, $filter6);

        $this->assertCount(6, $groups);
        $this->assertCount(1, $groups['Filter 1']);
        $this->assertCount(1, $groups['Filter 2']);
        $this->assertCount(1, $groups['Filter 3']);
        $this->assertEmpty($groups['Filter 4']);
        $this->assertCount(2, $groups['Filter 5']);
        $this->assertEmpty($groups['Filter 6']);

        $groups = $collection->groupBy(true, $filter1, $filter2, $filter3, $filter4, $filter5, $filter6);
        $this->assertCount(4, $groups);
        $this->assertCount(1, $groups['Filter 1']);
        $this->assertCount(1, $groups['Filter 2']);
        $this->assertCount(1, $groups['Filter 3']);
        $this->assertCount(2, $groups['Filter 5']);
    }
}
