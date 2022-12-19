<?php
declare(strict_types=1);

namespace Kununu\Collection\Mapper;

final class MapperCallers
{
    private $fnGetId;
    private $fnGetValue;

    public function __construct(callable $fnGetId, callable $fnGetValue)
    {
        $this->fnGetId = $fnGetId;
        $this->fnGetValue = $fnGetValue;
    }

    public function fnGetId(): callable
    {
        return $this->fnGetId;
    }

    public function fnGetValue(): callable
    {
        return $this->fnGetValue;
    }
}
