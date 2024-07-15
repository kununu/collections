<?php
declare(strict_types=1);

namespace Kununu\Collection;

use Kununu\Collection\Filter\CollectionFilter;

interface FilterableCollection extends Collection
{
    public function filter(CollectionFilter $filter): self|static;

    public function groupBy(bool $removeEmptyGroups, CollectionFilter ...$filters): array;
}
