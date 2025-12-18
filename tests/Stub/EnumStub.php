<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\EnumKeyValue\EnumFromNameTrait;
use Kununu\Collection\EnumKeyValue\EnumKeyInterface;
use Kununu\Collection\EnumKeyValue\EnumKeyRequiredTrait;
use Kununu\Collection\EnumKeyValue\Required;

enum EnumStub implements EnumKeyInterface
{
    use EnumFromNameTrait;
    use EnumKeyRequiredTrait;

    #[Required]
    case Key1;
    case Key2;
    case Key3;

    public function key(): string
    {
        return $this->name;
    }
}
