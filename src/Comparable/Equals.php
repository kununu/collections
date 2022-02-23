<?php
declare(strict_types=1);

namespace Kununu\Collection\Comparable;

interface Equals
{
    public function equals($other): bool;
}
