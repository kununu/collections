<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\EnumKeyValue\EnumKeyTrait;

enum IntEnumStub: int
{
    use EnumKeyTrait;

    case Option1 = 1;
    case Option2 = 2;
    case Option3 = 3;
}
