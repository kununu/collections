<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Mapper;

use InvalidArgumentException;
use Kununu\Collection\Tests\Stub\CollectionStub;
use Kununu\Collection\Tests\Stub\DTOCollectionStub;
use Kununu\Collection\Tests\Stub\DTOStub;
use Kununu\Collection\Tests\Stub\MapperStub;
use PHPUnit\Framework\TestCase;

final class DefaultMapperTest extends TestCase
{
    public function testMapper(): void
    {
        $mapper = new MapperStub(DTOCollectionStub::class);

        self::assertEquals(
            [
                'key 1' => 100,
                'key 2' => 101,
                'key 3' => 102,
            ],
            $mapper->map(
                new DTOCollectionStub(
                    new DTOStub('key 1', 100),
                    new DTOStub('key 2', 101),
                    new DTOStub('key 3', 102)
                )
            )
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid collection');

        $mapper->map(new CollectionStub());
    }

    public function testMapperWithInvalidCallerRegistration(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid collection class: Kununu\Collection\Tests\Stub\CollectionStub');

        self::assertNull(new MapperStub(CollectionStub::class));
    }
}
