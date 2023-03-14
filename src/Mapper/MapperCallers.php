<?php
declare(strict_types=1);

namespace Kununu\Collection\Mapper;

use Closure;

final class MapperCallers
{
    public function __construct(private Closure $fnGetId, private Closure $fnGetValue)
    {
    }

    public function fnGetId(): Closure
    {
        return $this->fnGetId;
    }

    public function fnGetValue(): Closure
    {
        return $this->fnGetValue;
    }
}
