<?php
declare(strict_types=1);

namespace Kununu\Collection;

use BadMethodCallException;
use OutOfBoundsException;

abstract class AbstractBasicItem
{
    protected const SETTER_PREFIX = 'set';
    protected const GETTER_PREFIX = 'get';
    protected const PROPERTIES = [];

    private array $attributes = [];

    public function __construct(array $attributes = [])
    {
        foreach ($this->getAllProperties() as $field) {
            $this->attributes[$field] = null;
        }

        $this->setAttributes($attributes);
    }

    public function __call(string $method, array $args)
    {
        return match (true) {
            $this->matches($method, static::SETTER_PREFIX) => $this->setAttribute(
                $this->getAttributeFromMethod($method, static::SETTER_PREFIX),
                current($args)
            ),
            $this->matches($method, static::GETTER_PREFIX) => $this->getAttribute(
                $this->getAttributeFromMethod($method, static::GETTER_PREFIX)
            ),
            default                                        => $this->throwBadMethodCallException($method),
        };
    }

    protected function setAttributes(array $attributes): self|static
    {
        foreach ($attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        return $this;
    }

    protected function setAttribute(string $name, $value): self|static
    {
        return $this->checkAttribute(
            $name,
            function() use ($name, $value): self|static {
                $this->attributes[$name] = $value;

                return $this;
            }
        );
    }

    protected function getAttribute(string $name): mixed
    {
        return $this->checkAttribute(
            $name,
            fn(): mixed => $this->attributes[$name]
        );
    }

    protected function getAllProperties(): array
    {
        $properties = static::PROPERTIES;
        foreach (class_parents($this) as $parentClass) {
            $properties = array_merge($properties, $parentClass::PROPERTIES);
        }

        return $properties;
    }

    private function matches(string $method, string $prefix): bool
    {
        return str_starts_with($method, $prefix);
    }

    private function getAttributeFromMethod(string $method, string $prefix): string
    {
        return lcfirst(substr($method, strlen($prefix)));
    }

    private function checkAttribute(string $name, callable $fn): mixed
    {
        if (array_key_exists($name, $this->attributes)) {
            return $fn();
        }

        throw new OutOfBoundsException(sprintf('%s : Invalid attribute "%s"', static::class, $name));
    }

    private function throwBadMethodCallException(string $method): void
    {
        throw new BadMethodCallException(sprintf('%s: Invalid method "%s" called', static::class, $method));
    }
}
