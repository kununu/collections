<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests;

use BadMethodCallException;
use DateTime;
use InvalidArgumentException;
use Kununu\Collection\Tests\Stub\AbstractItemStub;
use Kununu\Collection\Tests\Stub\AbstractItemWithCollectionsStub;
use Kununu\Collection\Tests\Stub\AbstractItemWithConditionalBuilderStub;
use Kununu\Collection\Tests\Stub\AbstractItemWithRequiredFieldsStub;
use Kununu\Collection\Tests\Stub\DTOCollectionStub;
use Kununu\Collection\Tests\Stub\DTOStub;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class AbstractItemTest extends TestCase
{
    public function testItemCreation(): void
    {
        $item = new AbstractItemStub(['name' => 'My Name']);

        $this->assertNull($item->getId());
        $this->assertEquals('My Name', $item->getName());
        $this->assertNull($item->getCreatedAt());
        $this->assertNull($item->getSimpleName());
        $this->assertNull($item->getVerified());
        $this->assertNull($item->getIndustryId());
        $this->assertNull($item->getSalary());

        $item->setId(100);
        $this->assertSame(100, $item->getId());

        $item->setCreatedAt($createdAt = new DateTime());
        $this->assertSame($createdAt, $item->getCreatedAt());

        $item->setSimpleName('Simple name');
        $this->assertSame('Simple name', $item->getSimpleName());

        $item->setVerified(true);
        $this->assertTrue($item->getVerified());

        $item->setIndustryId(15);
        $this->assertSame(15, $item->getIndustryId());

        $item->setSalary(1500.29);
        $this->assertSame(1500.29, $item->getSalary());
    }

    /** @dataProvider itemBuildDataProvider */
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

        $this->assertSame($expectedId, $item->getId());
        $this->assertNotNull($item->getId());
        $this->assertSame($expectedName, $item->getName());
        $this->assertEquals($expectedCreatedAt, $item->getCreatedAt());
        $this->assertNull($item->getExtraFieldNotUsedInBuild());
        $this->assertSame($expectedSimpleName, $item->getSimpleName());
        $this->assertSame($expectedVerified, $item->getVerified());
        $this->assertNotNull($item->getVerified());
        $this->assertSame($expectedIndustryId, $item->getIndustryId());
        $this->assertSame($expectedSalary, $item->getSalary());
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

    /** @dataProvider itemBuildRequiredDataProvider */
    public function testItemBuildRequired(array $data, ?string $expectedExceptionMessage): void
    {
        if (null !== $expectedExceptionMessage) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $item = AbstractItemWithRequiredFieldsStub::build($data);

        if (null === $expectedExceptionMessage) {
            $this->assertIsInt($item->giveMeTheId());
            $this->assertIsString($item->giveMeTheName());
            $this->assertInstanceOf(DateTime::class, $item->giveMeTheCreatedAt());
            $this->assertIsBool($item->giveMeTheVerified());
            $this->assertIsBool($item->giveMeTheVerified());
            $this->assertInstanceOf(DTOStub::class, $item->giveMeTheCustom());
            $this->assertIsFloat($item->giveMeTheScore());
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

    public function testItemBuildCollection(): void
    {
        $item = AbstractItemWithCollectionsStub::build([
            'collection' => [
                ['field' => 'field 1', 'value' => 100],
                ['field' => 'field 2', 'value' => 'A string'],
                ['field' => 'field 3', 'value' => 49.2],
            ],
        ]);

        $this->assertInstanceOf(DTOCollectionStub::class, $item->collection());
        $this->assertCount(3, $item->collection());
        $this->assertSame(
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
        $this->assertNull($item->notCollection());
        $this->assertInstanceOf(DTOCollectionStub::class, $item->defaultCollection());
        $this->assertEmpty($item->defaultCollection());
    }

    /** @dataProvider itemBuildConditionalDataProvider */
    public function testItemBuildConditional(mixed $expected): void
    {
        $item = AbstractItemWithConditionalBuilderStub::build([
            'source' => $this->dataName(),
            'value'  => 12.5,
        ]);

        $this->assertSame($expected, $item->value());
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
}
