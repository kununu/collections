<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests;

use Kununu\Collection\Tests\Stub\AbstractItemToArrayStub;
use PHPUnit\Framework\TestCase;

final class AbstractItemToArrayTest extends TestCase
{
    public function testAbstractItemToArray(): void
    {
        $item = AbstractItemToArrayStub::build([
            'id'         => 50,
            'name'       => 'My Name is?',
            'industryId' => 13,
            'verified'   => true,
            'extraData'  => [
                'id'          => 99,
                'description' => 'I really do not know',
            ],
        ]);

        self::assertEquals(
            [
                'id'         => 50,
                'name'       => '1000: My Name is?',
                'industryId' => 13,
                'verified'   => true,
                'extraData'  => [
                    'id'   => 99,
                    'data' => '99: I really do not know',
                ],
            ],
            $item->toArray()
        );
    }
}
