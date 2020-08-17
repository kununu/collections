<?php declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use ArrayIterator;
use Kununu\Collection\FilterableCollectionTrait;

final class FilterableCollectionTraitStub extends ArrayIterator
{
    use FilterableCollectionTrait;
}
