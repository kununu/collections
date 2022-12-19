<?php
declare(strict_types=1);

namespace Kununu\Collection\Mapper;

use Kununu\Collection\AbstractCollection;

interface Mapper
{
    public function map(AbstractCollection $collection): array;
}
