<?php
declare(strict_types=1);

namespace Kununu\Collection;

use BadMethodCallException;
use OutOfBoundsException;

abstract class AbstractItem
{
    protected const SETTER_PREFIX = 'set';
    protected const GETTER_PREFIX = 'get';

    protected const PROPERTIES = [];

    private $attributes = [];

    public function __construct(array $attributes = [])
    {
        $properties = static::PROPERTIES;
        foreach (class_parents($this) as $parentClass) {
            $properties = array_merge($properties, $parentClass::PROPERTIES);
        }

        foreach ($properties as $field) {
            $this->attributes[$field] = null;
        }

        $this->setAttributes($attributes);
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function build(array $data): self
    {
        $instance = new static();

        foreach (static::getBuilders() as $field => $builderCallable) {
            $setter = sprintf('%s%s', static::SETTER_PREFIX, ucfirst($field));
            $instance->{$setter}(is_callable($builderCallable) ? $builderCallable($data) : null);
        }

        return $instance;
    }

    public function __call(string $method, array $args)
    {
        switch (true) {
            case static::SETTER_PREFIX === substr($method, 0, $setterPrefixLen = strlen(static::SETTER_PREFIX)):
                $set = true;
                $attribute = lcfirst(substr($method, $setterPrefixLen));
                $value = current($args);
                break;

            case static::GETTER_PREFIX === substr($method, 0, $getterPrefixLen = strlen(static::GETTER_PREFIX)):
                $set = false;
                $attribute = lcfirst(substr($method, $getterPrefixLen));
                $value = null;
                break;

            default:
                throw new BadMethodCallException(sprintf('%s: Invalid method "%s" called', get_class($this), $method));
        }

        return $set ? $this->setAttribute($attribute, $value) : $this->getAttribute($attribute);
    }

    protected static function buildStringGetter(string $fieldName): callable
    {
        return function(array $data) use ($fieldName): ?string {
            return isset($data[$fieldName]) ? (string) $data[$fieldName] : null;
        };
    }

    protected static function buildBoolGetter(string $fieldName): callable
    {
        return function(array $data) use ($fieldName): ?bool {
            return isset($data[$fieldName]) ? (bool) $data[$fieldName] : null;
        };
    }

    protected static function buildIntGetter(string $fieldName): callable
    {
        return function(array $data) use ($fieldName): ?int {
            return isset($data[$fieldName]) ? (int) $data[$fieldName] : null;
        };
    }

    /**
     * Ready to be rewritten in your subclass!
     *
     * @codeCoverageIgnore
     * @return array
     *  [
     *      'itemProperty' => function(array $data) { return $valueForTheProperty; }
     *  ]
     */
    protected static function getBuilders(): array
    {
        return [];
    }

    /**
     * @param array $attributes
     *
     * @return static
     */
    protected function setAttributes(array $attributes): self
    {
        foreach ($attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param        $value
     *
     * @return static
     */
    protected function setAttribute(string $name, $value): self
    {
        $this->checkAttribute($name);
        $this->attributes[$name] = $value;

        return $this;
    }

    protected function getAttribute($name)
    {
        $this->checkAttribute($name);

        return $this->attributes[$name];
    }

    private function checkAttribute(string $name): void
    {
        if (!array_key_exists($name, $this->attributes)) {
            throw new OutOfBoundsException(sprintf('%s : Invalid attribute "%s"', get_class($this), $name));
        }
    }
}
