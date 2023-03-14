<?php
declare(strict_types=1);

namespace Kununu\Collection\Filter;

/**
 * Filter collection by several filters
 */
final class CompositeFilter extends BaseFilter
{
    /** @var CollectionFilter[] */
    private array $filters;

    public function __construct(string $key, private FilterOperator $filterOperator, CollectionFilter ...$filters)
    {
        parent::__construct($key);
        $this->filters = $filters;
    }

    public function isSatisfiedBy(FilterItem $item): bool
    {
        $result = $this->filterOperator->initialValue();
        $exitConditionValue = $this->filterOperator->exitConditionValue();

        foreach ($this->filters as $filter) {
            $result = $this->filterOperator->calculate($result, $filter->isSatisfiedBy($item));
            if ($result === $exitConditionValue) {
                return $exitConditionValue;
            }
        }

        return $result;
    }
}
