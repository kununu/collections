<?php
declare(strict_types=1);

namespace Kununu\Collection\Mapper;

use Closure;

final readonly class MapperCallers
{
    public function __construct(public Closure $fnGetId, public Closure $fnGetValue)
    {
    }
}
