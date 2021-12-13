<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests;

use BadMethodCallException;
use DateTime;
use InvalidArgumentException;
use Kununu\Collection\Tests\Stub\AbstractItemStub;
use Kununu\Collection\Tests\Stub\AbstractItemWithRequiredFieldsStub;
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

        $item->setId(100);
        $this->assertEquals(100, $item->getId());

        $item->setCreatedAt($createdAt = new DateTime());
        $this->assertEquals($createdAt, $item->getCreatedAt());

        $item->setSimpleName('Simple name');
        $this->assertEquals('Simple name', $item->getSimpleName());

        $item->setVerified(true);
        $this->assertTrue($item->getVerified());

        $item->setIndustryId(15);
        $this->assertEquals(15, $item->getIndustryId());
    }

    /**
     * @dataProvider itemBuildDataProvider
     *
     * @param array         $data
     * @param int|null      $expectedId
     * @param string|null   $expectedName
     * @param DateTime|null $expectedCreatedAt
     * @param string|null   $expectedSimpleName
     * @param bool|null     $expectedVerified
     * @param int|null      $expectedIndustryId
     */
    public function testItemBuild(
        array $data,
        ?int $expectedId,
        ?string $expectedName,
        ?DateTime $expectedCreatedAt,
        ?string $expectedSimpleName,
        ?bool $expectedVerified,
        ?int $expectedIndustryId
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
    }

    public function itemBuildDataProvider(): array
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
            ],
            'some_fields_3'   => [
                [
                    'id'         => 10,
                    'name'       => 'My Name',
                    'simpleName' => 'Simple name',
                ],
                10,
                'My Name',
                null,
                'Simple name',
                false,
                null,
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
                ],
                10,
                'My Name',
                $createdAtDateTime,
                'Simple name',
                true,
                10,
            ],
        ];
    }

    /**
     * @dataProvider itemBuildRequiredDataProvider
     *
     * @param array       $data
     * @param string|null $expectedExceptionMessage
     *
     * @return void
     */
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
        }
    }

    public function itemBuildRequiredDataProvider(): array
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
            'all_fields'         => [
                [
                    'id'        => 1,
                    'name'      => 'The name',
                    'createdAt' => '2021-12-13 12:00:00',
                    'verified'  => true,
                    'custom'    => 50.25,
                ],
                null,
            ],
        ];
    }

    public function testItemInvalidMethod(): void
    {
        $item = new AbstractItemStub();

        $this->expectExceptionMessage(BadMethodCallException::class);
        $this->expectExceptionMessage('Kununu\Collection\Tests\Stub\AbstractItemStub: Invalid method "thisMethodReallyDoesNotExists" called');
        $item->thisMethodReallyDoesNotExists();
    }

    public function testItemSetInvalidProperty(): void
    {
        $item = new AbstractItemStub();

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Kununu\Collection\Tests\Stub\AbstractItemStub : Invalid attribute "invalidProperty"');
        $item->setInvalidProperty(true);
    }

    public function testItemGetInvalidProperty(): void
    {
        $item = new AbstractItemStub();

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Kununu\Collection\Tests\Stub\AbstractItemStub : Invalid attribute "invalidProperty"');
        $item->getInvalidProperty(true);
    }

    public function testItemBuilderInvalidProperty(): void
    {
        $item = new AbstractItemStub();

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Kununu\Collection\Tests\Stub\AbstractItemStub: Invalid method "withInvalidProperty" called');
        $item->withInvalidProperty(false);
    }
}
