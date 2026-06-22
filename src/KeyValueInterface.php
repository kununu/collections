<?php
declare(strict_types=1);

namespace Kununu\Collection;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Kununu\Collection\Convertible\FromArray;
use Kununu\Collection\Convertible\FromIterable;
use Kununu\Collection\Convertible\ToArray;

interface KeyValueInterface extends ArrayAccess, Countable, IteratorAggregate, FromArray, FromIterable, ToArray
{
}
