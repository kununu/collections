<?php
declare(strict_types=1);

namespace Kununu\Collection\Mapper;

use Closure;

final class MapperCallers
{
    public function __construct(public readonly Closure $fnGetId, public readonly Closure $fnGetValue)
    {
    }
}
