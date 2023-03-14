<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\Mapper\DefaultMapper;
use Kununu\Collection\Mapper\MapperCallers;

final class MapperStub extends DefaultMapper
{
    protected function getCallers(string $collectionClass): ?MapperCallers
    {
        if (DTOCollectionStub::class === $collectionClass) {
            return new MapperCallers(
                fn(DTOStub $dto): string => $dto->field(),
                fn(DTOStub $dto): int => $dto->value()
            );
        }

        return null;
    }
}
