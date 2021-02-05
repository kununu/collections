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

        $item->setId(100);
        $this->assertEquals(100, $item->getId());

        $item->setCreatedAt($createdAt = new DateTime());
        $this->assertEquals($createdAt, $item->getCreatedAt());
    }

    /**
     * @dataProvider itemBuildDataProvider
     *
     * @param array         $data
     * @param int|null      $expectedId
     * @param string|null   $expectedName
     * @param DateTime|null $expectedCreatedAt
     */
    public function testItemBuild(
        array $data,
        ?int $expectedId,
        ?string $expectedName,
        ?DateTime $expectedCreatedAt
    ): void {
        $item = AbstractItemStub::build($data);

        $this->assertEquals($expectedId, $item->getId());
        $this->assertEquals($expectedName, $item->getName());
        $this->assertEquals($expectedCreatedAt, $item->getCreatedAt());
        $this->assertNull($item->getExtraFieldNotUsedInBuild());
    }

    public function itemBuildDataProvider(): array
    {
        return [
            'all_null_fields' => [
                [],
                null,
                null,
                null,
            ],
            'some_fields_1'   => [
                [
                    'name' => 'My Name',
                ],
                null,
                'My Name',
                null,
            ],
            'some_fields_2'   => [
                [
                    'name'      => 'My Name',
                    'createdAt' => $createdAt = new DateTime(),
                ],
                null,
                'My Name',
                $createdAt,
            ],
            'some_fields_3'   => [
                [
                    'id'   => 10,
                    'name' => 'My Name',
                ],
                10,
                'My Name',
                null,
            ],
            'all_fields'      => [
                [
                    'id'                       => 10,
                    'name'                     => 'My Name',
                    'createdAt'                => $createdAt,
                    'extraFieldNotUsedInBuild' => 'THIS VALUE WILL NOT BE USED IN BUILD',
                ],
                10,
                'My Name',
                $createdAt,
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
