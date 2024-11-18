<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests;

use ArrayIterator;
use Exception;
use Generator;
use Kununu\Collection\Tests\Stub\AutoSortedCollectionStub;
use Kununu\Collection\Tests\Stub\CollectionStub;
use Kununu\Collection\Tests\Stub\ToArrayStub;
use Kununu\Collection\Tests\Stub\ToIntStub;
use Kununu\Collection\Tests\Stub\ToStringStub;
use PHPUnit\Framework\TestCase;
use Throwable;

final class CollectionTest extends TestCase
{
    /**
     * @dataProvider fromIterableDataProvider
     *
     * @param iterable $data
     * @param array    $expected
     */
    public function testFromIterable(iterable $data, array $expected): void
    {
        $this->assertEquals($expected, CollectionStub::fromIterable($data)->toArray());
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

    /**
     * @dataProvider chunkDataProvider
     */
    public function testChunk(CollectionStub $collection, int $chunkSize, array $expectedChunks): void
    {
        $this->assertEquals($expectedChunks, $collection->chunk($chunkSize));
    }

    public static function chunkDataProvider(): array
    {
        return [
            'chunk_size_0' => [
                CollectionStub::fromIterable([1, 2, 3, 4, 5]),
                0,
                [
                    CollectionStub::fromIterable([1, 2, 3, 4, 5]),
                ],
            ],
            'chunk_size_2' => [
                CollectionStub::fromIterable([1, 2, 3, 4, 5]),
                2,
                [
                    CollectionStub::fromIterable([1, 2]),
                    CollectionStub::fromIterable([3, 4]),
                    CollectionStub::fromIterable([5]),
                ],
            ],
            'chunk_size_1' => [
                CollectionStub::fromIterable([1, 2, 3, 4, 5]),
                1,
                [
                    CollectionStub::fromIterable([1]),
                    CollectionStub::fromIterable([2]),
                    CollectionStub::fromIterable([3]),
                    CollectionStub::fromIterable([4]),
                    CollectionStub::fromIterable([5]),
                ],
            ],
            'chunk_size_5' => [
                CollectionStub::fromIterable([1, 2, 3, 4, 5]),
                5,
                [
                    CollectionStub::fromIterable([1, 2, 3, 4, 5]),
                ],
            ],
        ];
    }

    /**
     * @dataProvider chunkEachDataProvider
     */
    public function testChunkEach(CollectionStub $collection, int $chunkSize, array $expectedChunks): void
    {
        $i = 0;
        $collection->eachChunk(
            $chunkSize,
            function(CollectionStub $collection) use (&$i, $expectedChunks): void {
                $this->assertEquals($collection, $expectedChunks[$i++]);
            }
        );
    }

    public static function chunkEachDataProvider(): array
    {
        return [
            'chunk_each_size_0' => [
                CollectionStub::fromIterable([1, 2, 3, 4, 5]),
                0,
                [
                    CollectionStub::fromIterable([1, 2, 3, 4, 5]),
                ],
            ],
            'chunk_each_size_2' => [
                CollectionStub::fromIterable([1, 2, 3, 4, 5]),
                2,
                [
                    CollectionStub::fromIterable([1, 2]),
                    CollectionStub::fromIterable([3, 4]),
                    CollectionStub::fromIterable([5]),
                ],
            ],
            'chunk_each_size_1' => [
                CollectionStub::fromIterable([1, 2, 3, 4, 5]),
                1,
                [
                    CollectionStub::fromIterable([1]),
                    CollectionStub::fromIterable([2]),
                    CollectionStub::fromIterable([3]),
                    CollectionStub::fromIterable([4]),
                    CollectionStub::fromIterable([5]),
                ],
            ],
            'chunk_each_size_5' => [
                CollectionStub::fromIterable([1, 2, 3, 4, 5]),
                5,
                [
                    CollectionStub::fromIterable([1, 2, 3, 4, 5]),
                ],
            ],
        ];
    }

    public function testEmpty(): void
    {
        $collection = new CollectionStub();

        $this->assertTrue($collection->empty());

        $collection->add(1);
        $this->assertFalse($collection->empty());
    }

    public function testUnique(): void
    {
        $collection = CollectionStub::fromIterable(
            $this->getGenerator(1, 2, 3, 4, 1, 2, 2, 3, 3, 4, 2, 1, 2, 1, 1, 2)
        );

        $uniqueCollection = $collection->unique();

        $this->assertEquals(16, $collection->count());
        $this->assertEquals(4, $uniqueCollection->count());
        $this->assertEquals([1, 2, 3, 4], $uniqueCollection->toArray());
    }

    public function testReverse(): void
    {
        $this->assertEquals([5, 4, 3, 2, 1], CollectionStub::fromIterable([1, 2, 3, 4, 5])->reverse()->toArray());
    }

    public function testDiff(): void
    {
        $collection1 = CollectionStub::fromIterable([1, 2, 3, 4, 5]);
        $collection2 = CollectionStub::fromIterable([1, 2, 3, 6, 7]);

        $this->assertEquals([4, 5], $collection1->diff($collection2)->toArray());
        $this->assertEquals([6, 7], $collection2->diff($collection1)->toArray());
    }

    /**
     * @dataProvider eachDataProvider
     *
     * @param bool     $rewind
     * @param int|null $expectedCurrent
     * @param bool     $expectException
     */
    public function testEach(bool $rewind, ?int $expectedCurrent, bool $expectException): void
    {
        $exceptionWasThrown = false;
        $exception = new Exception('An error inside each');
        $collectedValues = [];
        $collection = CollectionStub::fromIterable($this->getGenerator(1, 2, 3, 4, 5));

        try {
            $collection->each(
                function(int $value) use (&$collectedValues, $expectException, $exception): void {
                    if ($value === 3 && $expectException) {
                        throw $exception;
                    }
                    $collectedValues[] = $value * 2;
                },
                $rewind
            );
        } catch (Throwable $e) {
            $exceptionWasThrown = true;
            $this->assertEquals($e, $exception);
        }

        $this->assertEquals($exceptionWasThrown, $expectException);
        $this->assertEquals($expectException ? [2, 4] : [2, 4, 6, 8, 10], $collectedValues);
        $this->assertEquals($expectedCurrent, $collection->current());
    }

    public function eachDataProvider(): array
    {
        return [
            'rewind'              => [
                true,
                1,
                false,
            ],
            'no_rewind'           => [
                false,
                null,
                false,
            ],
            'exception_rewind'    => [
                true,
                1,
                true,
            ],
            'exception_no_rewind' => [
                false,
                3,
                true,
            ],
        ];
    }

    /**
     * @dataProvider mapDataProvider
     *
     * @param bool       $rewind
     * @param array|null $expectedCurrent
     * @param bool       $expectException
     */
    public function testMap(bool $rewind, ?array $expectedCurrent, bool $expectException): void
    {
        $exceptionWasThrown = false;
        $exception = new Exception('An error inside map');

        $collection = CollectionStub::fromIterable($this->getGenerator(['id' => 1], ['id' => 2], ['id' => 3]));

        $mapResult = null;
        try {
            $mapResult = $collection->map(
                function(array $value) use ($expectException, $exception): int {
                    if ($value['id'] === 3 && $expectException) {
                        throw $exception;
                    }

                    return (int) $value['id'];
                },
                $rewind
            );
        } catch (Throwable $e) {
            $exceptionWasThrown = true;
            $this->assertEquals($e, $exception);
        }

        $this->assertEquals($exceptionWasThrown, $expectException);
        $this->assertEquals($expectException ? null : [1, 2, 3], $mapResult);
        $this->assertEquals($expectedCurrent, $collection->current());
    }

    public function mapDataProvider(): array
    {
        return [
            'rewind'              => [
                true,
                ['id' => 1],
                false,
            ],
            'no_rewind'           => [
                false,
                null,
                false,
            ],
            'exception_rewind'    => [
                true,
                ['id' => 1],
                true,
            ],
            'exception_no_rewind' => [
                false,
                ['id' => 3],
                true,
            ],
        ];
    }

    /**
     * @dataProvider reduceDataProvider
     *
     * @param bool     $rewind
     * @param int|null $expectedCurrent
     * @param bool     $expectException
     */
    public function testReduce(bool $rewind, ?int $expectedCurrent, bool $expectException): void
    {
        $exceptionWasThrown = false;
        $exception = new Exception('An error inside reduce');

        $collection = CollectionStub::fromIterable($this->getGenerator(1, 2, 3, 4, 5));

        $value = null;
        try {
            $value = $collection->reduce(
                function(int $carry, int $element) use ($expectException, $exception): int {
                    if ($element === 3 && $expectException) {
                        throw $exception;
                    }

                    return $carry + $element;
                },
                1000,
                $rewind
            );
        } catch (Throwable $e) {
            $exceptionWasThrown = true;
            $this->assertEquals($e, $exception);
        }

        $this->assertEquals($exceptionWasThrown, $expectException);
        $this->assertEquals($expectException ? null : 1015, $value);
        $this->assertEquals($expectedCurrent, $collection->current());
    }

    public function reduceDataProvider(): array
    {
        return [
            'rewind'              => [
                true,
                1,
                false,
            ],
            'no_rewind'           => [
                false,
                null,
                false,
            ],
            'exception_rewind'    => [
                true,
                1,
                true,
            ],
            'exception_no_rewind' => [
                false,
                3,
                true,
            ],
        ];
    }

    /**
     * @dataProvider autoSortedCollectionDataProvider
     *
     * @param iterable $data
     * @param array    $expected
     *
     * @return void
     */
    public function testAutoSortedCollection(iterable $data, array $expected): void
    {
        $this->assertEquals($expected, AutoSortedCollectionStub::fromIterable($data)->toArray());
    }

    public function autoSortedCollectionDataProvider(): array
    {
        return [
            [
                $this->getGenerator(1, 2, 3, 4, 1, 2, 2, 3, 3, 4, 2, 1, 2, 1, 1, 2),
                [1, 2, 3, 4],
            ],
            [
                range(5, 10),
                [5, 6, 7, 8, 9, 10],
            ],
            [
                $this->getGenerator(9, 14, 5),
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
                $this->getGenerator('x', 'm', 'd', 'h', 'f'),
                ['d', 'f', 'h', 'm', 'x'],
            ],
        ];
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
