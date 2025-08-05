<?php
declare(strict_types=1);

namespace Kununu\Collection;

abstract class AbstractItem extends AbstractBasicItem
{
    use AbstractItemBuildersTrait;

    public const string DATE_FORMAT = 'Y-m-d H:i:s';

    public static function build(array $data): self|static
    {
        // @phpstan-ignore new.static
        $instance = new static();

        foreach (static::getBuilders() as $field => $builderCallable) {
            $setter = sprintf('%s%s', static::SETTER_PREFIX, ucfirst($field));
            $instance->{$setter}(is_callable($builderCallable) ? $builderCallable($data) : null);
        }

        return $instance;
    }

    /**
     * Must be implemented in your subclass!
     *
     * Array format:
     * [
     *  'itemProperty' => fn(array $data): mixed => $valueForTheProperty
     * ]
     */
    abstract protected static function getBuilders(): array;
}
