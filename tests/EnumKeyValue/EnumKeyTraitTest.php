<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\EnumKeyValue;

use Kununu\Collection\EnumKeyValue\Exception\NotAnEnumException;
use Kununu\Collection\Tests\Stub\EnumStub;
use Kununu\Collection\Tests\Stub\IntEnumStub;
use Kununu\Collection\Tests\Stub\NotAnEnumStub;
use Kununu\Collection\Tests\Stub\StringEnumStub;
use PHPUnit\Framework\TestCase;

final class EnumKeyTraitTest extends TestCase
{
    public function testBasicEnum(): void
    {
        self::assertEquals('Key1', EnumStub::Key1->key());
        self::assertEquals('Key2', EnumStub::Key2->key());
        self::assertEquals('Key3', EnumStub::Key3->key());
    }

    public function testIntEnum(): void
    {
        self::assertEquals('Option1', IntEnumStub::Option1->key());
        self::assertEquals('Option2', IntEnumStub::Option2->key());
        self::assertEquals('Option3', IntEnumStub::Option3->key());
    }

    public function testStringEnum(): void
    {
        self::assertEquals('KEY1', StringEnumStub::Key1->key());
        self::assertEquals('KEY2', StringEnumStub::Key2->key());
        self::assertEquals('KEY3', StringEnumStub::Key3->key());
    }

    public function testNotAnEnum(): void
    {
        $this->expectException(NotAnEnumException::class);
        $this->expectExceptionMessage(sprintf('"%s" is not an enum', NotAnEnumStub::class));
        $this->expectExceptionCode(400);

        (new NotAnEnumStub())->key();
    }
}
