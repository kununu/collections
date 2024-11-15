<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests;

use ArrayIterator;
use Exception;
use Generator;
use InvalidArgumentException;
use Kununu\Collection\Collection;
use Kununu\Collection\Tests\Stub\AbstractItemStub;
use Kununu\Collection\Tests\Stub\AutoSortedCollectionStub;
use Kununu\Collection\Tests\Stub\CollectionStub;
use Kununu\Collection\Tests\Stub\DTOCollectionStub;
use Kununu\Collection\Tests\Stub\DTOStub;
use Kununu\Collection\Tests\Stub\FilterableCollectionStub;
use Kununu\Collection\Tests\Stub\StringableStub;
use Kununu\Collection\Tests\Stub\ToArrayStub;
use Kununu\Collection\Tests\Stub\ToIntStub;
use Kununu\Collection\Tests\Stub\ToStringStub;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;

final class CollectionTest extends TestCase
{
    #[DataProvider('fromIterableDataProvider')]
    public function testFromIterable(iterable $data, array $expected): void
    {
        self::assertEquals($expected, CollectionStub::fromIterable($data)->toArray());
    }

    public static function fromIterableDataProvider(): array
    {
        return [
            'simple_integers_array_case_1' => [
                [1, 2, 3],
                [1, 2, 3],
            ],
            'simple_integers_array_case_2' => [
                range(5, 10),
                [5, 6, 7, 8, 9, 10],
            ],
            'generator_of_integers'        => [
                self::getGenerator(5, 9, 14),
                [5, 9, 14],
            ],
            'array_iterator_of_integers'   => [
                self::getArrayIterator(10, 20, 30, 40),
                [10, 20, 30, 40],
            ],
            'empty_array_iterator'         => [
                self::getArrayIterator(),
                [],
            ],
            'generator_of_complex_objects' => [
                self::getGenerator(
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
            'array_of_objects'             => [
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
            'generator_of_simple_objects'  => [
                self::getGenerator(
                    ToStringStub::create(ToIntStub::fromInt(1), 'ABC'),
                    ToStringStub::create(ToIntStub::fromInt(2), 'DEF'),
                    ToStringStub::create(ToIntStub::fromInt(3), 'GHI'),
                    ToStringStub::create(ToIntStub::fromInt(4), 'JKL'),
                    StringableStub::create(ToIntStub::fromInt(5), 'MNO')
                ),
                [
                    '1: ABC',
                    '2: DEF',
                    '3: GHI',
                    '4: JKL',
                    '5: MNO',
                ],
            ],
        ];
    }

    #[DataProvider('hasDataProvider')]
    public function testHas(iterable $collectionContents, mixed $hasValue, bool $hasStrict, bool $expectedHas): void
    {
        $collection = CollectionStub::fromIterable($collectionContents);

        self::assertEquals($expectedHas, $collection->has($hasValue, $hasStrict));
    }

    public static function hasDataProvider(): array
    {
        return [
            'has_with_integers_strict'                   => [
                [1, 2, 3],
                1,
                true,
                true,
            ],
            'has_with_integers_loose'                    => [
                [1, 2, 3],
                '1',
                false,
                true,
            ],
            'missing_with_integers_strict'               => [
                [1, 2, 3],
                4,
                true,
                false,
            ],
            'missing_with_integers_strict_string_to_int' => [
                [1, 2, 3],
                '1',
                true,
                false,
            ],
            'has_with_strings_strict'                    => [
                ['one', 'two', 'three'],
                'one',
                true,
                true,
            ],
            'missing_with_strings_strict'                => [
                ['one', 'two', 'three'],
                'missing',
                true,
                false,
            ],
            'has_with_strings_loose'                     => [
                ['one', 'two', 'three'],
                'one',
                false,
                true,
            ],
            'missing_with_strings_loose'                 => [
                ['one', 'two', 'three'],
                'missing',
                false,
                false,
            ],
            'has_with_object_strict'                     => [
                [
                    $one = new AbstractItemStub(['name' => 'one']),
                    new AbstractItemStub(['name' => 'two']),
                    new AbstractItemStub(['name' => 'three']),
                ],
                $one,
                true,
                true,
            ],
            'missing_with_object_strict'                 => [
                [
                    new AbstractItemStub(['name' => 'one']),
                    new AbstractItemStub(['name' => 'two']),
                    new AbstractItemStub(['name' => 'three']),
                ],
                new AbstractItemStub(['name' => 'one']),
                true,
                false,
            ],
            'has_with_object_loose'                      => [
                [
                    new AbstractItemStub(['name' => 'one']),
                    new AbstractItemStub(['name' => 'two']),
                    new AbstractItemStub(['name' => 'three']),
                ],
                new AbstractItemStub(['name' => 'one']),
                false,
                true,
            ],
            'missing_with_object_loose'                  => [
                [
                    new AbstractItemStub(['name' => 'one']),
                    new AbstractItemStub(['name' => 'two']),
                    new AbstractItemStub(['name' => 'three']),
                ],
                new AbstractItemStub(['name' => 'vier']),
                false,
                false,
            ],
        ];
    }

    public function testEmpty(): void
    {
        $collection = new CollectionStub();

        self::assertTrue($collection->empty());

        $collection->add(1);

        self::assertFalse($collection->empty());
    }

    public function testUnique(): void
    {
        $collection = CollectionStub::fromIterable(
            $this->getGenerator(1, 2, 3, 4, 1, 2, 2, 3, 3, 4, 2, 1, 2, 1, 1, 2)
        );

        $uniqueCollection = $collection->unique();

        self::assertEquals(16, $collection->count());
        self::assertEquals(4, $uniqueCollection->count());
        self::assertEquals([1, 2, 3, 4], $uniqueCollection->toArray());
    }

    #[DataProvider('keysDataProvider')]
    public function testKeys(Collection $collection, array $expected): void
    {
        self::assertEquals($expected, $collection->keys());
    }

    public static function keysDataProvider(): array
    {
        return [
            'collection_with_no_offset_set'          => [
                CollectionStub::fromIterable(self::getGenerator(1, 2, 3, 4)),
                [0, 1, 2, 3],
            ],
            'auto_sorted_collection_with_offset_set' => [
                AutoSortedCollectionStub::fromIterable(self::getArrayIterator('the', 'quick', 'brown', 'fox')),
                ['brown', 'fox', 'quick', 'the'],
            ],
            'dto_collection_with_offset_set'         => [
                new DTOCollectionStub(
                    new DTOStub('key 1', 100),
                    new DTOStub('key 2', 101),
                    new DTOStub('key 3', 102)
                ),
                ['key 1', 'key 2', 'key 3'],
            ],
        ];
    }

    #[DataProvider('valuesDataProvider')]
    public function testValues(Collection $collection, array $expected): void
    {
        self::assertEquals($expected, $collection->values());
    }

    public static function valuesDataProvider(): array
    {
        return [
            'integer_collection'     => [
                CollectionStub::fromIterable(self::getGenerator(1, 2, 3, 4)),
                [1, 2, 3, 4],
            ],
            'auto_sorted_collection' => [
                AutoSortedCollectionStub::fromIterable(self::getArrayIterator('the', 'quick', 'brown', 'fox')),
                ['brown', 'fox', 'quick', 'the'],
            ],
            'dto_collection'         => [
                new DTOCollectionStub(
                    $item3 = new DTOStub('key 3', 102),
                    $item2 = new DTOStub('key 2', 101),
                    $item1 = new DTOStub('key 1', 100)
                ),
                [$item3, $item2, $item1],
            ],
        ];
    }

    #[DataProvider('duplicatesDataProvider')]
    public function testDuplicates(bool $strict, bool $uniques, array $contents, array $expected): void
    {
        $collection = CollectionStub::fromIterable($contents);
        $duplicateCollection = $collection->duplicates($strict, $uniques);

        self::assertEquals($expected, $duplicateCollection->toArray());
    }

    public static function duplicatesDataProvider(): array
    {
        return [
            'integers_no_duplicates_strict'        => [
                true,
                false,
                [1, 2, 3],
                [],
            ],
            'integers_duplicates_strict'           => [
                true,
                false,
                [1, 2, 3, 4, 1, 2, 4],
                [1, 2, 4],
            ],
            'integers_duplicates_strict_mixed'     => [
                true,
                false,
                [1, 2, 3, 4, 1, '2', 4],
                [1, 4],
            ],
            'strings_no_duplicates_strict'         => [
                true,
                false,
                [
                    '7178e84e-472d-4766-8e45-a571871ff988',
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d',
                    '909a8214-86b1-4d78-94f2-6a8e22a24020',
                    'baaeace4-1aeb-4b47-ac2c-4b90ebd12342',
                ],
                [],
            ],
            'strings_duplicates_strict_no_uniques' => [
                true,
                false,
                [
                    '7178e84e-472d-4766-8e45-a571871ff988',
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-1
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-2
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d',
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d', // dup 2-1
                    '909a8214-86b1-4d78-94f2-6a8e22a24020',
                    'baaeace4-1aeb-4b47-ac2c-4b90ebd12342',
                ],
                [
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-1
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-2
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d', // dup 2-1
                ],
            ],
            'strings_duplicates_strict_uniques'    => [
                true,
                true,
                [
                    '7178e84e-472d-4766-8e45-a571871ff988',
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-1
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-2
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d',
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d', // dup 2-1
                    '909a8214-86b1-4d78-94f2-6a8e22a24020',
                    'baaeace4-1aeb-4b47-ac2c-4b90ebd12342',
                ],
                [
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-1 and dup 1-2
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d', // dup 2-1
                ],
            ],
            'item_stubs_no_duplicates_strict'      => [
                true,
                false,
                [
                    new AbstractItemStub(['name' => 'one']),
                    new AbstractItemStub(['name' => 'two']),
                    new AbstractItemStub(['name' => 'three']),
                ],
                [],
            ],
            'item_stubs_duplicates_strict'         => [
                true,
                false,
                [
                    $one = new AbstractItemStub(['name' => 'one']),
                    $one,
                    $two = new AbstractItemStub(['name' => 'two']),
                    $two,
                    new AbstractItemStub(['name' => 'three']),
                ],
                [
                    $one,
                    $two,
                ],
            ],
            'integers_no_duplicates_loose'         => [
                false,
                false,
                [1, 2, 3],
                [],
            ],
            'integers_duplicates_loose'            => [
                false,
                false,
                [1, 2, 3, 4, 1, '2', 4],
                ['1', 2, '4'],
            ],
            'strings_no_duplicates_loose'          => [
                false,
                false,
                [
                    '7178e84e-472d-4766-8e45-a571871ff988',
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d',
                    '909a8214-86b1-4d78-94f2-6a8e22a24020',
                    'baaeace4-1aeb-4b47-ac2c-4b90ebd12342',
                ],
                [],
            ],
            'strings_duplicates_loose_no_uniques'  => [
                false,
                false,
                [
                    '7178e84e-472d-4766-8e45-a571871ff988',
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-1
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-2
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d',
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d', // dup 2-1
                    '909a8214-86b1-4d78-94f2-6a8e22a24020',
                    'baaeace4-1aeb-4b47-ac2c-4b90ebd12342',
                    '2',
                    2,
                ],
                [
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-1
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-2
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d', // dup 2-1
                    2,
                ],
            ],
            'strings_duplicates_loose_uniques'     => [
                false,
                true,
                [
                    '7178e84e-472d-4766-8e45-a571871ff988',
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-1
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-2
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d',
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d', // dup 2-1
                    '909a8214-86b1-4d78-94f2-6a8e22a24020',
                    'baaeace4-1aeb-4b47-ac2c-4b90ebd12342',
                    '2',
                    2,
                ],
                [
                    '7178e84e-472d-4766-8e45-a571871ff988', // dup 1-1 and dup 1-2
                    'ca583de8-e6ba-4098-a0bf-10c981ff3d8d', // dup 2-1
                    2,
                ],
            ],
            'item_stubs_no_duplicates_loose'       => [
                false,
                false,
                [
                    new AbstractItemStub(['name' => 'one']),
                    new AbstractItemStub(['name' => 'two']),
                    new AbstractItemStub(['name' => 'three']),
                ],
                [],
            ],
            'item_stubs_duplicates_loose'          => [
                false,
                false,
                [
                    new AbstractItemStub(['name' => 'one']),
                    new AbstractItemStub(['name' => 'one']),
                    new AbstractItemStub(['name' => 'two']),
                    new AbstractItemStub(['name' => 'two']),
                    new AbstractItemStub(['name' => 'three']),
                ],
                [
                    new AbstractItemStub(['name' => 'one']),
                    new AbstractItemStub(['name' => 'two']),
                ],
            ],
        ];
    }

    public function testReverse(): void
    {
        self::assertEquals([5, 4, 3, 2, 1], CollectionStub::fromIterable([1, 2, 3, 4, 5])->reverse()->toArray());
    }

    #[DataProvider('chunkDataProvider')]
    public function testChunk(Collection $collectionToChunk, int $chunkSize, array $expectedChunks): void
    {
        self::assertEquals(
            $expectedChunks,
            $collectionToChunk->chunk($chunkSize)
        );
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

    public function testDiff(): void
    {
        $collection1 = CollectionStub::fromIterable([1, 2, 3, 4, 5]);
        $collection2 = CollectionStub::fromIterable([1, 2, 3, 6, 7]);

        self::assertEquals([4, 5], $collection1->diff($collection2)->toArray());
        self::assertEquals([6, 7], $collection2->diff($collection1)->toArray());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Other collection must be of the same type');

        $collection1->diff(new FilterableCollectionStub());
    }

    #[DataProvider('eachDataProvider')]
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
            self::assertEquals($e, $exception);
        }

        self::assertEquals($exceptionWasThrown, $expectException);
        self::assertEquals($expectException ? [2, 4] : [2, 4, 6, 8, 10], $collectedValues);
        self::assertEquals($expectedCurrent, $collection->current());
    }

    public static function eachDataProvider(): array
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

    #[DataProvider('mapDataProvider')]
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
            self::assertEquals($e, $exception);
        }

        self::assertEquals($exceptionWasThrown, $expectException);
        self::assertEquals($expectException ? null : [1, 2, 3], $mapResult);
        self::assertEquals($expectedCurrent, $collection->current());
    }

    public static function mapDataProvider(): array
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

    #[DataProvider('reduceDataProvider')]
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
            self::assertEquals($e, $exception);
        }

        self::assertEquals($exceptionWasThrown, $expectException);
        self::assertEquals($expectException ? null : 1015, $value);
        self::assertEquals($expectedCurrent, $collection->current());
    }

    public static function reduceDataProvider(): array
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

    #[DataProvider('autoSortedCollectionDataProvider')]
    public function testAutoSortedCollection(iterable $data, array $expected): void
    {
        self::assertEquals($expected, AutoSortedCollectionStub::fromIterable($data)->toArray());
    }

    public static function autoSortedCollectionDataProvider(): array
    {
        return [
            'generator_of_integers_with_duplicated_items' => [
                self::getGenerator(1, 2, 3, 4, 1, 2, 2, 3, 3, 4, 2, 1, 2, 1, 1, 2),
                [1, 2, 3, 4],
            ],
            'array_of_integers'                           => [
                range(5, 10),
                [5, 6, 7, 8, 9, 10],
            ],
            'generator_of_unsorted_integers'              => [
                self::getGenerator(9, 14, 5),
                [5, 9, 14],
            ],
            'array_iterator_of_integers'                  => [
                self::getArrayIterator(10, 20, 30, 40),
                [10, 20, 30, 40],
            ],
            'empty_array_iterator'                        => [
                self::getArrayIterator(),
                [],
            ],
            'empty_generator'                             => [
                self::getGenerator(),
                [],
            ],
            'generator_of_unsorted_strings'               => [
                self::getGenerator('x', 'm', 'd', 'h', 'f'),
                ['d', 'f', 'h', 'm', 'x'],
            ],
        ];
    }

    private static function getGenerator(mixed ...$items): Generator
    {
        foreach ($items as $item) {
            yield $item;
        }
    }

    private static function getArrayIterator(mixed ...$items): ArrayIterator
    {
        $arrayIterator = new ArrayIterator();

        foreach ($items as $item) {
            $arrayIterator->append($item);
        }

        return $arrayIterator;
    }
}
