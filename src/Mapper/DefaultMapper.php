<?php
declare(strict_types=1);

namespace Kununu\Collection\Mapper;

use Closure;
use InvalidArgumentException;
use Kununu\Collection\AbstractCollection;

abstract class DefaultMapper implements Mapper
{
    /** @var MapperCallers[] */
    private array $callers = [];

    public function __construct(string ...$collectionClasses)
    {
        foreach ($collectionClasses as $collectionClass) {
            if (!$callers = $this->getCallers($collectionClass)) {
                throw new InvalidArgumentException(sprintf('Invalid collection class: %s', $collectionClass));
            }
            $this->callers[$collectionClass] = $callers;
        }
    }

    public function map(AbstractCollection $collection): array
    {
        if (!array_key_exists($collectionClass = get_class($collection), $this->callers)) {
            throw new InvalidArgumentException('Invalid collection');
        }

        $callers = $this->callers[$collectionClass];

        return $this->mapCollection($collection, $callers->fnGetId(), $callers->fnGetValue());
    }

    abstract protected function getCallers(string $collectionClass): ?MapperCallers;

    private function mapCollection(AbstractCollection $collection, Closure $fnGetId, Closure $fnGetValue): array
    {
        $result = [];
        foreach ($collection as $item) {
            $result[$fnGetId($item)] = $fnGetValue($item);
        }

        return $result;
    }
}
