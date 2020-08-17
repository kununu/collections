<?php declare(strict_types=1);

namespace Kununu\Collection\Tests;

use ArrayIterator;
use Generator;
use Kununu\Collection\Tests\Stub\CollectionTraitStub;
use Kununu\Collection\Tests\Stub\ToArrayStub;
use Kununu\Collection\Tests\Stub\ToIntStub;
use Kununu\Collection\Tests\Stub\ToStringStub;
use PHPUnit\Framework\TestCase;

final class CollectionTraitTest extends TestCase
{
    /**
     * @dataProvider fromIterableDataProvider
     *
     * @param iterable $data
     * @param array    $expected
     */
    public function testFromIterable(iterable $data, array $expected): void
    {
        $collection = CollectionTraitStub::fromIterable($data);

        $this->assertEquals($expected, $collection->toArray());
    }

    public function fromIterableDataProvider(): array
    {
        return [
            [
                [1, 2, 3],
                [1, 2, 3],
            ],
            [
                range(5, 10),
                [5, 6, 7, 8, 9, 10],
            ],
            [
                $this->getGenerator(5, 9, 14),
                [5, 9, 14],
            ],
            [
                $this->getArrayIterator(10, 20, 30, 40),
                [10, 20, 30, 40],
            ],
            [
                $this->getArrayIterator(),
                [],
            ],
            [
                $this->getGenerator(
                    ToArrayStub::create(ToIntStub::fromInt(1), ToStringStub::create(ToIntStub::fromInt(7), 'PHP')),
                    ToArrayStub::create(ToIntStub::fromInt(2), ToStringStub::create(ToIntStub::fromInt(13), 'Java')),
                    ToArrayStub::create(ToIntStub::fromInt(3), ToStringStub::create(ToIntStub::fromInt(7), 'C#'))
                ),
                [
                    [
                        'id'   => 1,
                        'data' => '7: PHP',
                    ],
                    [
                        'id'   => 2,
                        'data' => '13: Java',
                    ],
                    [
                        'id'   => 3,
                        'data' => '7: C#',
                    ],
                ],
            ],
            [
                [
                    ToIntStub::fromInt(1),
                    ToIntStub::fromInt(2),
                    ToIntStub::fromInt(3),
                ],
                [
                    1,
                    2,
                    3,
                ],
            ],
            [
                $this->getGenerator(
                    ToStringStub::create(ToIntStub::fromInt(1), 'ABC'),
                    ToStringStub::create(ToIntStub::fromInt(2), 'DEF'),
                    ToStringStub::create(ToIntStub::fromInt(3), 'GHI'),
                    ToStringStub::create(ToIntStub::fromInt(4), 'JKL')
                ),
                [
                    '1: ABC',
                    '2: DEF',
                    '3: GHI',
                    '4: JKL',
                ],
            ],
        ];
    }

    public function testEmpty(): void
    {
        $collection = new CollectionTraitStub();

        $this->assertTrue($collection->empty());

        $collection->add(1);
        $this->assertFalse($collection->empty());
    }

    public function testUnique(): void
    {
        $collection = CollectionTraitStub::fromIterable(
            $this->getGenerator(1, 2, 3, 4, 1, 2, 2, 3, 3, 4, 2, 1, 2, 1, 1, 2)
        );

        $uniqueCollection = $collection->unique();

        $this->assertEquals(16, $collection->count());
        $this->assertEquals(4, $uniqueCollection->count());
        $this->assertEquals([1, 2, 3, 4], $uniqueCollection->toArray());
    }

    public function testReverse(): void
    {
        $collection = CollectionTraitStub::fromIterable([1, 2, 3, 4, 5]);
        $this->assertEquals([5, 4, 3, 2, 1], $collection->reverse()->toArray());
    }

    public function testDiff(): void
    {
        $collection1 = CollectionTraitStub::fromIterable([1, 2, 3, 4, 5]);
        $collection2 = CollectionTraitStub::fromIterable([1, 2, 3, 6, 7]);

        $this->assertEquals([4, 5], $collection1->diff($collection2)->toArray());
        $this->assertEquals([6, 7], $collection2->diff($collection1)->toArray());
    }

    private function getGenerator(...$items): Generator
    {
        foreach ($items as $item) {
            yield $item;
        }
    }

    private function getArrayIterator(...$items): ArrayIterator
    {
        $arrayIterator = new ArrayIterator();

        foreach ($items as $item) {
            $arrayIterator->append($item);
        }

        return $arrayIterator;
    }
}
