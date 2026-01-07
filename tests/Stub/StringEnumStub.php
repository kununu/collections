<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\EnumKeyValue\EnumKeyTrait;

enum StringEnumStub: string
{
    use EnumKeyTrait;

    case Key1 = 'KEY1';
    case Key2 = 'KEY2';
    case Key3 = 'KEY3';
}
