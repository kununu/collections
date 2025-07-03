<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests;

use ArrayIterator;
use ArrayObject;
use Generator;
use Kununu\Collection\KeyValue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class KeyValueTest extends TestCase
{
    private const VALUES = [
        'name'  => 'My Name',
        'age'   => 23,
        'score' => 4.2,
    ];

    private const ITERABLE_DATA = [
        'Hello',
        2    => 'World',
        'id' => 5000,
    ];

    private KeyValue $keyValue;

    public function testFromArray(): void
    {
        $keyValue = KeyValue::fromArray(self::ITERABLE_DATA);

        self::assertEquals(array_keys(self::ITERABLE_DATA), $keyValue->keys());
        self::assertEquals(array_values(self::ITERABLE_DATA), $keyValue->values());
    }

    #[DataProvider('fromIterableDataProvider')]
    public function testFromIterable(iterable $iterable): void
    {
        $keyValue = KeyValue::fromIterable($iterable);

        self::assertEquals(array_keys(self::ITERABLE_DATA), $keyValue->keys());
        self::assertEquals(array_values(self::ITERABLE_DATA), $keyValue->values());
    }

    public static function fromIterableDataProvider(): array
    {
        return [
            'array'          => [
                self::ITERABLE_DATA,
            ],
            'array_iterator' => [
                new ArrayIterator(self::ITERABLE_DATA),
            ],
            'array_object'   => [
                new ArrayObject(self::ITERABLE_DATA),
            ],
            'generator'      => [
                self::iterableToGenerator(self::ITERABLE_DATA),
            ],
            'itself'         => [
                KeyValue::fromArray(self::ITERABLE_DATA),
            ],
        ];
    }

    public function testValues(): void
    {
        self::assertEquals(array_values(self::VALUES), $this->keyValue->values());
    }

    public function testKeys(): void
    {
        self::assertEquals(array_keys(self::VALUES), $this->keyValue->keys());
    }

    public function testGet(): void
    {
        self::assertNull($this->keyValue->get(0));
        self::assertEquals('NOPE', $this->keyValue->get(0, 'NOPE'));
        self::assertEquals(5555, $this->keyValue->get(0, 5555));
        self::assertEquals('My Name', $this->keyValue->get('name'));
        self::assertEquals(23, $this->keyValue->get('age'));
        self::assertEquals(4.2, $this->keyValue->get('score'));
    }

    public function testHas(): void
    {
        self::assertFalse($this->keyValue->has(0));
        self::assertTrue($this->keyValue->has('name'));
        self::assertTrue($this->keyValue->has('age'));
        self::assertTrue($this->keyValue->has('score'));
        self::assertFalse($this->keyValue->has('salary'));
    }

    public function testRemove(): void
    {
        $keyValue = KeyValue::fromArray(self::VALUES);

        self::assertFalse($keyValue->has('salary'));

        $keyValue
            ->remove('name')
            ->remove('salary');

        self::assertFalse($keyValue->has('name'));
        self::assertTrue($keyValue->has('age'));
        self::assertTrue($keyValue->has('score'));
        self::assertFalse($keyValue->has('salary'));
    }

    public function testSet(): void
    {
        $keyValue = KeyValue::fromArray(self::VALUES)
            ->set('name', 'Not My Name')
            ->set('age', $this->keyValue->get('age') * 2);

        self::assertEquals('Not My Name', $keyValue->get('name'));
        self::assertEquals(46, $keyValue->get('age'));
        self::assertEquals(4.2, $keyValue->get('score'));
    }

    public function testToArray(): void
    {
        self::assertEquals(self::VALUES, $this->keyValue->toArray());
    }

    public function testCount(): void
    {
        self::assertEquals(3, $this->keyValue->count());
        self::assertCount(3, $this->keyValue);
    }

    public function testGetIterator(): void
    {
        self::assertEquals(new ArrayIterator(self::VALUES), $this->keyValue->getIterator());
        self::assertEquals(self::VALUES, iterator_to_array($this->keyValue));
    }

    public function testOffsetExists(): void
    {
        self::assertFalse($this->keyValue->offsetExists(0));
        self::assertTrue($this->keyValue->offsetExists('name'));
        self::assertTrue($this->keyValue->offsetExists('age'));
        self::assertTrue($this->keyValue->offsetExists('score'));
        self::assertFalse($this->keyValue->offsetExists('salary'));
    }

    public function testOffsetGet(): void
    {
        self::assertNull($this->keyValue->offsetGet(0));
        self::assertNull($this->keyValue[0]);

        self::assertEquals('My Name', $this->keyValue->offsetGet('name'));
        self::assertEquals('My Name', $this->keyValue['name']);

        self::assertEquals(23, $this->keyValue->offsetGet('age'));
        self::assertEquals(23, $this->keyValue['age']);

        self::assertEquals(4.2, $this->keyValue->offsetGet('score'));
        self::assertEquals(4.2, $this->keyValue['score']);
    }

    public function testOffsetSet(): void
    {
        $keyValue = KeyValue::fromArray(self::VALUES);

        $keyValue->offsetSet('name', 'Not My Name');
        $keyValue['age'] = $this->keyValue['age'] * 2;

        self::assertEquals('Not My Name', $keyValue->get('name'));
        self::assertEquals(46, $keyValue->get('age'));
        self::assertEquals(4.2, $keyValue->get('score'));
    }

    public function testOffsetUnset(): void
    {
        $keyValue = KeyValue::fromArray(self::VALUES);

        self::assertFalse($keyValue->has('salary'));

        $keyValue->offsetUnset('name');
        unset($keyValue['salary']);

        self::assertFalse($keyValue->has('name'));
        self::assertTrue($keyValue->has('age'));
        self::assertTrue($keyValue->has('score'));
        self::assertFalse($keyValue->has('salary'));
    }

    protected function setUp(): void
    {
        $this->keyValue = KeyValue::fromArray(self::VALUES);
    }

    private static function iterableToGenerator(iterable $iterable): Generator
    {
        foreach ($iterable as $key => $item) {
            yield $key => $item;
        }
    }
}
