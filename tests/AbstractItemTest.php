<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests;

use BadMethodCallException;
use DateTime;
use Kununu\Collection\Tests\Stub\AbstractItemStub;
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
        $this->assertEquals(true, $item->getVerified());

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

        $this->assertEquals($expectedId, $item->getId());
        $this->assertEquals($expectedName, $item->getName());
        $this->assertEquals($expectedCreatedAt, $item->getCreatedAt());
        $this->assertNull($item->getExtraFieldNotUsedInBuild());
        $this->assertEquals($expectedSimpleName, $item->getSimpleName());
        $this->assertEquals($expectedVerified, $item->getVerified());
        $this->assertEquals($expectedIndustryId, $item->getIndustryId());
    }

    public function itemBuildDataProvider(): array
    {
        return [
            'all_null_fields' => [
                [],
                null,
                null,
                null,
                null,
                null,
                null,
            ],
            'some_fields_1'   => [
                [
                    'name' => 'My Name',
                    'verified' => 0,
                ],
                null,
                'My Name',
                null,
                null,
                false,
                null,
            ],
            'some_fields_2'   => [
                [
                    'name'      => 'My Name',
                    'createdAt' => $createdAt = new DateTime(),
                    'industryId' => '1',
                ],
                null,
                'My Name',
                $createdAt,
                null,
                null,
                1,
            ],
            'some_fields_3'   => [
                [
                    'id'   => 10,
                    'name' => 'My Name',
                    'simpleName' => 'Simple name',
                ],
                10,
                'My Name',
                null,
                'Simple name',
                null,
                null,
            ],
            'all_fields'      => [
                [
                    'id'                       => 10,
                    'name'                     => 'My Name',
                    'createdAt'                => $createdAt,
                    'extraFieldNotUsedInBuild' => 'THIS VALUE WILL NOT BE USED IN BUILD',
                    'simpleName' => 'Simple name',
                    'verified' => true,
                    'industryId' => 10,
                ],
                10,
                'My Name',
                $createdAt,
                'Simple name',
                true,
                10,
            ],
        ];
    }

    public function testItemErrors(): void
    {
        $item = new AbstractItemStub();

        $this->expectExceptionMessage(BadMethodCallException::class);
        $this->expectExceptionMessage('Kununu\Collection\Tests\Stub\AbstractItemStub: Invalid method "thisMethodReallyDoesNotExists" called');
        $item->thisMethodReallyDoesNotExists();

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Kununu\Collection\Tests\Stub\AbstractItemStub : Invalid attribute "invalidProperty"');
        $item->setInvalidProperty(true);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Kununu\Collection\Tests\Stub\AbstractItemStub : Invalid attribute "invalidProperty"');
        $item->getInvalidProperty(true);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Kununu\Collection\Tests\Stub\AbstractItemStub : Invalid attribute "invalidProperty"');
        $item->withInvalidProperty(false);
    }
}
