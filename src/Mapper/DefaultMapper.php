<?php
declare(strict_types=1);

namespace Kununu\Collection\Mapper;

use Closure;
use InvalidArgumentException;
use Kununu\Collection\AbstractCollection;

abstract class DefaultMapper implements Mapper
{
    /** @var array<string, MapperCallers> */
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
        if (!array_key_exists($collectionClass = $collection::class, $this->callers)) {
            throw new InvalidArgumentException('Invalid collection');
        }

        $collectionCallers = $this->callers[$collectionClass];

        return $this->mapCollection($collection, $collectionCallers->fnGetId, $collectionCallers->fnGetValue);
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
