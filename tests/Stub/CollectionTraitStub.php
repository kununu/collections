<?php declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use ArrayIterator;
use Kununu\Collection\CollectionTrait;

final class CollectionTraitStub extends ArrayIterator
{
    use CollectionTrait;
}
