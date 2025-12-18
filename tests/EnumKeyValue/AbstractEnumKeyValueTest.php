<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\EnumKeyValue;

use BadMethodCallException;
use Kununu\Collection\EnumKeyValue\Exception\RemovingRequiredKeyException;
use Kununu\Collection\EnumKeyValue\Exception\RequiredKeyMissingException;
use Kununu\Collection\KeyValue;
use Kununu\Collection\Tests\Stub\EnumKeyValueStub;
use Kununu\Collection\Tests\Stub\EnumStub;
use PHPUnit\Framework\TestCase;
use ValueError;

final class AbstractEnumKeyValueTest extends TestCase
{
    private const string KEY_1 = EnumStub::Key1->name;
    private const int KEY_1_VALUE = 1234;

    private const string KEY_2 = EnumStub::Key2->name;
    private const string KEY_2_VALUE = 'hello';

    private const string KEY_3 = EnumStub::Key3->name;
    private const float KEY_3_VALUE = 5.895;

    private const array VALUES = [
        self::KEY_1 => self::KEY_1_VALUE,
        self::KEY_2 => self::KEY_2_VALUE,
        self::KEY_3 => self::KEY_3_VALUE,
    ];

    private const string INVALID_ENUM_CASE_MESSAGE = '"%s" is not a valid case for enum "%s"';
    private const string KEY_INVALID = 'Invalid';

    private string $invalidCaseMessage;
    private EnumKeyValueStub $keyValue;
    private EnumKeyValueStub $keyValueNoRequired;

    public function testFromArray(): void
    {
        $kv = EnumKeyValueStub::fromArray(self::VALUES);

        self::assertEquals([EnumStub::Key1, EnumStub::Key2, EnumStub::Key3], $kv->keys(false));
        self::assertEquals([self::KEY_1_VALUE, self::KEY_2_VALUE, self::KEY_3_VALUE], $kv->values());
    }

    public function testFromIterable(): void
    {
        $kv = EnumKeyValueStub::fromIterable(KeyValue::fromIterable(self::VALUES));

        self::assertEquals([EnumStub::Key1, EnumStub::Key2, EnumStub::Key3], $kv->keys(false));
        self::assertEquals([self::KEY_1_VALUE, self::KEY_2_VALUE, self::KEY_3_VALUE], $kv->values());
    }

    public function testCount(): void
    {
        self::assertCount(2, $this->keyValue);
        self::assertEquals(2, $this->keyValue->count());
        self::assertEquals(2, count($this->keyValue));
    }

    public function testGetWithValidKeys(): void
    {
        self::assertEquals(self::KEY_1_VALUE, $this->keyValue->get(EnumStub::Key1));
        self::assertEquals(self::KEY_1_VALUE, $this->keyValue->get(self::KEY_1));
        self::assertEquals(self::KEY_1_VALUE, $this->keyValue->getKey1());

        self::assertEquals(self::KEY_2_VALUE, $this->keyValue->get(EnumStub::Key2));
        self::assertEquals(self::KEY_2_VALUE, $this->keyValue->get(self::KEY_2));
        self::assertEquals(self::KEY_2_VALUE, $this->keyValue->getKey2());

        self::assertNull($this->keyValue->get(EnumStub::Key3));
        self::assertEquals(self::KEY_3_VALUE, $this->keyValue->get(self::KEY_3, self::KEY_3_VALUE));
        self::assertNull($this->keyValue->getKey3());
    }

    public function testGetWithInvalidKey(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage($this->invalidCaseMessage);

        $this->keyValue->get(self::KEY_INVALID);
    }

    public function testGetWithMissingRequiredKey(): void
    {
        $this->expectException(RequiredKeyMissingException::class);
        $this->expectExceptionMessage(sprintf('Missing required key: "%s"', EnumStub::Key1->name));
        $this->expectExceptionCode(400);

        $this->keyValueNoRequired->get(EnumStub::Key1);
    }

    public function testGetIterator(): void
    {
        $result = [];
        $iterator = $this->keyValue->getIterator();
        while ($item = $iterator->current()) {
            $result[$iterator->key()] = $item;
            $iterator->next();
        }

        self::assertEquals(
            [
                self::KEY_1 => self::KEY_1_VALUE,
                self::KEY_2 => self::KEY_2_VALUE,
            ],
            $result
        );
    }

    public function testHasWithValidKeys(): void
    {
        self::assertTrue($this->keyValue->has(EnumStub::Key1));
        self::assertTrue($this->keyValue->has(self::KEY_1));
        self::assertTrue($this->keyValue->hasKey1());

        self::assertTrue($this->keyValue->has(EnumStub::Key2));
        self::assertTrue($this->keyValue->has(self::KEY_2));
        self::assertTrue($this->keyValue->hasKey2());

        self::assertFalse($this->keyValue->has(EnumStub::Key3));
        self::assertFalse($this->keyValue->has(self::KEY_3));
        self::assertFalse($this->keyValue->hasKey3());
    }

    public function testHasWithInvalidKey(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage($this->invalidCaseMessage);

        $this->keyValue->has(self::KEY_INVALID);
    }

    public function testKeys(): void
    {
        self::assertEquals([EnumStub::Key1, EnumStub::Key2], $this->keyValue->keys(false));
        self::assertEquals([self::KEY_1, self::KEY_2], $this->keyValue->keys());
    }

    public function testRemoveWithValidKeys(): void
    {
        self::assertTrue($this->keyValue->hasKey2());
        self::assertFalse($this->keyValue->hasKey3());

        self::assertFalse($this->keyValue->remove(EnumStub::Key2)->hasKey2());
        self::assertFalse($this->keyValue->removeKey3()->hasKey3());
    }

    public function testRemoveRequiredSetKey(): void
    {
        $this->expectException(RemovingRequiredKeyException::class);
        $this->expectExceptionMessage('Removing required key: "Key1"');

        $this->keyValue->remove(EnumStub::Key1);
    }

    public function testRemoveWithInvalidValidKey(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage($this->invalidCaseMessage);

        $this->keyValue->remove(self::KEY_INVALID);
    }

    public function testSetWithValidKeys(): void
    {
        $kv = new EnumKeyValueStub();

        self::assertFalse($kv->hasKey1());
        self::assertFalse($kv->hasKey2());
        self::assertFalse($kv->hasKey3());

        self::assertEquals(self::KEY_1_VALUE, $kv->set(self::KEY_1, self::KEY_1_VALUE)->getKey1());
        self::assertEquals(self::KEY_2_VALUE, $kv->set(EnumStub::Key2, self::KEY_2_VALUE)->getKey2());
        self::assertEquals(self::KEY_3_VALUE, $kv->setKey3(self::KEY_3_VALUE)->getKey3());
    }

    public function testSetWithInvalidValidKey(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage($this->invalidCaseMessage);

        $this->keyValue->set(self::KEY_INVALID, self::KEY_1_VALUE);
    }

    public function testToArray(): void
    {
        self::assertEquals(
            [
                self::KEY_1 => self::KEY_1_VALUE,
                self::KEY_2 => self::KEY_2_VALUE,
            ],
            $this->keyValue->toArray()
        );
    }

    public function testValues(): void
    {
        self::assertEquals([self::KEY_1_VALUE, self::KEY_2_VALUE], $this->keyValue->values());
    }

    public function testInvalidGetMethod(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage($this->invalidCaseMessage);

        (new EnumKeyValueStub())->getInvalid();
    }

    public function testInvalidHasMethod(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage($this->invalidCaseMessage);

        (new EnumKeyValueStub())->hasInvalid();
    }

    public function testInvalidSetMethod(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage($this->invalidCaseMessage);

        (new EnumKeyValueStub())->setInvalid(self::KEY_3_VALUE);
    }

    public function testUnknownMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(sprintf('%s: Invalid method "invalidMethod" called', EnumKeyValueStub::class));

        (new EnumKeyValueStub())->invalidMethod();
    }

    protected function setUp(): void
    {
        $this->invalidCaseMessage = sprintf(self::INVALID_ENUM_CASE_MESSAGE, self::KEY_INVALID, EnumStub::class);

        $this->keyValue = (new EnumKeyValueStub())
            ->setKey1(self::KEY_1_VALUE)
            ->setKey2(self::KEY_2_VALUE);

        $this->keyValueNoRequired = (new EnumKeyValueStub())
            ->setKey2(self::KEY_2_VALUE);
    }
}
