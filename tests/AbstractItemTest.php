<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests;

use BadMethodCallException;
use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use Kununu\Collection\Tests\Stub\AbstractItemSnakeCaseStub;
use Kununu\Collection\Tests\Stub\AbstractItemStub;
use Kununu\Collection\Tests\Stub\AbstractItemWithCollectionsStub;
use Kununu\Collection\Tests\Stub\AbstractItemWithConditionalBuilderStub;
use Kununu\Collection\Tests\Stub\AbstractItemWithFromArrayStub;
use Kununu\Collection\Tests\Stub\AbstractItemWithRequiredFieldsStub;
use Kununu\Collection\Tests\Stub\DTOCollectionStub;
use Kununu\Collection\Tests\Stub\DTOStub;
use Kununu\Collection\Tests\Stub\FromArrayStub;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AbstractItemTest extends TestCase
{
    public function testItemCreation(): void
    {
        $item = new AbstractItemStub(['name' => 'My Name']);

        self::assertNull($item->getId());
        self::assertEquals('My Name', $item->getName());
        self::assertNull($item->getCreatedAt());
        self::assertNull($item->getSimpleName());
        self::assertNull($item->getVerified());
        self::assertNull($item->getIndustryId());
        self::assertNull($item->getSalary());

        $item->setId(100);

        self::assertSame(100, $item->getId());

        $item->setCreatedAt($createdAt = new DateTime());

        self::assertSame($createdAt, $item->getCreatedAt());

        $item->setSimpleName('Simple name');

        self::assertSame('Simple name', $item->getSimpleName());

        $item->setVerified(true);

        self::assertTrue($item->getVerified());

        $item->setIndustryId(15);

        self::assertSame(15, $item->getIndustryId());

        $item->setSalary(1500.29);

        self::assertSame(1500.29, $item->getSalary());
    }

    #[DataProvider('itemBuildDataProvider')]
    public function testItemBuild(
        array $data,
        ?int $expectedId,
        ?string $expectedName,
        ?DateTime $expectedCreatedAt,
        ?string $expectedSimpleName,
        ?bool $expectedVerified,
        ?int $expectedIndustryId,
        ?float $expectedSalary
    ): void {
        $item = AbstractItemStub::build($data);

        self::assertSame($expectedId, $item->getId());
        self::assertNotNull($item->getId());
        self::assertSame($expectedName, $item->getName());
        self::assertEquals($expectedCreatedAt, $item->getCreatedAt());
        self::assertNull($item->getExtraFieldNotUsedInBuild());
        self::assertSame($expectedSimpleName, $item->getSimpleName());
        self::assertSame($expectedVerified, $item->getVerified());
        self::assertNotNull($item->getVerified());
        self::assertSame($expectedIndustryId, $item->getIndustryId());
        self::assertSame($expectedSalary, $item->getSalary());
    }

    public static function itemBuildDataProvider(): array
    {
        $createdAt = '2021-12-13 12:00:00';
        $createdAtDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $createdAt);

        return [
            'all_null_fields' => [
                [],
                0,
                null,
                null,
                null,
                false,
                null,
                1000.0,
            ],
            'some_fields_1'   => [
                [
                    'name'     => 'My Name',
                    'verified' => 0,
                ],
                0,
                'My Name',
                null,
                null,
                false,
                null,
                1000.0,
            ],
            'some_fields_2'   => [
                [
                    'name'       => 'My Name',
                    'createdAt'  => $createdAt,
                    'industryId' => '1',
                ],
                0,
                'My Name',
                $createdAtDateTime,
                null,
                false,
                1,
                1000.0,
            ],
            'some_fields_3'   => [
                [
                    'id'         => 10,
                    'name'       => 'My Name',
                    'simpleName' => 'Simple name',
                    'salary'     => 500.90,
                ],
                10,
                'My Name',
                null,
                'Simple name',
                false,
                null,
                500.90,
            ],
            'all_fields'      => [
                [
                    'id'                       => 10,
                    'name'                     => 'My Name',
                    'createdAt'                => $createdAt,
                    'extraFieldNotUsedInBuild' => 'THIS VALUE WILL NOT BE USED IN BUILD',
                    'simpleName'               => 'Simple name',
                    'verified'                 => true,
                    'industryId'               => 10,
                    'salary'                   => 1250.82,
                ],
                10,
                'My Name',
                $createdAtDateTime,
                'Simple name',
                true,
                10,
                1250.82,
            ],
        ];
    }

    #[DataProvider('itemBuildRequiredDataProvider')]
    public function testItemBuildRequired(array $data, ?string $expectedExceptionMessage): void
    {
        if (null !== $expectedExceptionMessage) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $item = AbstractItemWithRequiredFieldsStub::build($data);

        if (null === $expectedExceptionMessage) {
            self::assertIsInt($item->giveMeTheId());
            self::assertIsString($item->giveMeTheName());
            self::assertInstanceOf(DateTime::class, $item->giveMeTheCreatedAt());
            self::assertIsBool($item->giveMeTheVerified());
            self::assertIsBool($item->giveMeTheVerified());
            self::assertInstanceOf(DTOStub::class, $item->giveMeTheCustom());
            self::assertIsFloat($item->giveMeTheScore());
        }
    }

    public static function itemBuildRequiredDataProvider(): array
    {
        return [
            'missing_all_fields' => [
                [],
                'Missing "id" field',
            ],
            'missing_id'         => [
                [
                    'name' => 'The name',
                ],
                'Missing "id" field',
            ],
            'missing_name'       => [
                [
                    'id' => 1,
                ],
                'Missing "name" field',
            ],
            'missing_created_at' => [
                [
                    'id'   => 1,
                    'name' => 'The name',
                ],
                'Missing "createdAt" field',
            ],
            'missing_verified'   => [
                [
                    'id'        => 1,
                    'name'      => 'The name',
                    'createdAt' => '2021-12-13 12:00:00',
                ],
                'Missing "verified" field',
            ],
            'missing_custom'     => [
                [
                    'id'        => 1,
                    'name'      => 'The name',
                    'createdAt' => '2021-12-13 12:00:00',
                    'verified'  => true,
                ],
                'Missing "custom" field',
            ],
            'missing_score'      => [
                [
                    'id'        => 1,
                    'name'      => 'The name',
                    'createdAt' => '2021-12-13 12:00:00',
                    'verified'  => true,
                    'custom'    => 50.25,
                ],
                'Missing "score" field',
            ],
            'all_fields'         => [
                [
                    'id'        => 1,
                    'name'      => 'The name',
                    'createdAt' => '2021-12-13 12:00:00',
                    'verified'  => true,
                    'custom'    => 50.25,
                    'score'     => 4.9,
                ],
                null,
            ],
        ];
    }

    public function testItemBuildFromArray(): void
    {
        $item = AbstractItemWithFromArrayStub::build([
            'fromArray' => ['id' => 1, 'name' => 'The Name'],
        ]);

        self::assertInstanceOf(FromArrayStub::class, $item->fromArray());
        self::assertEquals(1, $item->fromArray()->id);
        self::assertEquals('The Name', $item->fromArray()->name);
        self::assertNull($item->notFromArray());
        self::assertInstanceOf(FromArrayStub::class, $item->defaultFromArray());
        self::assertEquals(0, $item->defaultFromArray()->id);
        self::assertEquals('', $item->defaultFromArray()->name);
    }

    public function testItemBuildCollection(): void
    {
        $item = AbstractItemWithCollectionsStub::build([
            'collection' => [
                ['field' => 'field 1', 'value' => 100],
                ['field' => 'field 2', 'value' => 'A string'],
                ['field' => 'field 3', 'value' => 49.2],
            ],
        ]);

        self::assertInstanceOf(DTOCollectionStub::class, $item->collection());
        self::assertCount(3, $item->collection());
        self::assertSame(
            [
                'field 1' => [
                    'field' => 'field 1',
                    'value' => 100,
                ],
                'field 2' => [
                    'field' => 'field 2',
                    'value' => 'A string',
                ],
                'field 3' => [
                    'field' => 'field 3',
                    'value' => 49.2,
                ],
            ],
            $item->collection()->toArray()
        );
        self::assertNull($item->notCollection());
        self::assertInstanceOf(DTOCollectionStub::class, $item->defaultCollection());
        self::assertEmpty($item->defaultCollection());
    }

    #[DataProvider('itemBuildConditionalDataProvider')]
    public function testItemBuildConditional(mixed $expected): void
    {
        $item = AbstractItemWithConditionalBuilderStub::build([
            'source' => $this->dataName(),
            'value'  => 12.5,
        ]);

        self::assertSame($expected, $item->value());
    }

    public static function itemBuildConditionalDataProvider(): array
    {
        return [
            'int'     => [12],
            'float'   => [12.5],
            'string'  => ['12.5'],
            'unknown' => [null],
        ];
    }

    public function testItemInvalidMethod(): void
    {
        $item = new AbstractItemStub();

        $this->expectExceptionMessage(BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Kununu\Collection\Tests\Stub\AbstractItemStub: Invalid method "thisMethodReallyDoesNotExists" called'
        );

        $item->thisMethodReallyDoesNotExists();
    }

    public function testItemSetInvalidProperty(): void
    {
        $item = new AbstractItemStub();

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'Kununu\Collection\Tests\Stub\AbstractItemStub : Invalid attribute "invalidProperty"'
        );

        $item->setInvalidProperty(true);
    }

    public function testItemGetInvalidProperty(): void
    {
        $item = new AbstractItemStub();

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage(
            'Kununu\Collection\Tests\Stub\AbstractItemStub : Invalid attribute "invalidProperty"'
        );

        $item->getInvalidProperty(true);
    }

    public function testItemBuilderInvalidProperty(): void
    {
        $item = new AbstractItemStub();

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Kununu\Collection\Tests\Stub\AbstractItemStub: Invalid method "withInvalidProperty" called'
        );

        $item->withInvalidProperty(false);
    }

    public function testItemBuilderWithSnakeCase(): void
    {
        $item = AbstractItemSnakeCaseStub::build([
            'string_field'                       => 'The string field',
            'required_string_field'              => 'The required string field',
            'bool_field'                         => true,
            'required_bool_field'                => false,
            'int_field'                          => 1,
            'required_int_field'                 => 2,
            'float_field'                        => 1.5,
            'required_float_field'               => 2.6,
            'date_time_field'                    => '2023-10-11 12:34:01',
            'required_date_time_field'           => '2023-10-11 12:34:02',
            'date_time_immutable_field'          => '2023-10-11 12:34:03',
            'required_date_time_immutable_field' => '2023-10-11 12:34:04',
        ]);

        self::assertEquals('The string field', $item->stringField());
        self::assertEquals('The required string field', $item->requiredStringField());
        self::assertTrue($item->boolField());
        self::assertFalse($item->requiredBoolField());
        self::assertEquals(1, $item->intField());
        self::assertEquals(2, $item->requiredIntField());
        self::assertEquals(1.5, $item->floatField());
        self::assertEquals(2.6, $item->requiredFloatField());
        self::assertEquals(
            DateTime::createFromFormat('Y-m-d H:i:s', '2023-10-11 12:34:01'),
            $item->dateTimeField()
        );
        self::assertEquals(
            DateTime::createFromFormat('Y-m-d H:i:s', '2023-10-11 12:34:02'),
            $item->requiredDateTimeField()
        );
        self::assertEquals(
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2023-10-11 12:34:03'),
            $item->dateTimeImmutableField()
        );
        self::assertEquals(
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2023-10-11 12:34:04'),
            $item->requiredDateTimeImmutableField()
        );
    }
}
