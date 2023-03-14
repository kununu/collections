<?php
declare(strict_types=1);

namespace Kununu\Collection;

use BadMethodCallException;
use OutOfBoundsException;

abstract class AbstractItem
{
    use AbstractItemBuildersTrait;

    public const DATE_FORMAT = 'Y-m-d H:i:s';

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

    public static function build(array $data): self|static
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
                throw new BadMethodCallException(sprintf('%s: Invalid method "%s" called', static::class, $method));
        }

        return $set ? $this->setAttribute($attribute, $value) : $this->getAttribute($attribute);
    }

    /**
     * @codeCoverageIgnore
     *
     * Ready to be rewritten in your subclass!
     *
     * Array format:
     * [
     *  'itemProperty' => fn(array $data) => $valueForTheProperty
     * ]
     */
    protected static function getBuilders(): array
    {
        return [];
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
        $this->checkAttribute($name);
        $this->attributes[$name] = $value;

        return $this;
    }

    protected function getAttribute($name): mixed
    {
        $this->checkAttribute($name);

        return $this->attributes[$name];
    }

    protected function getAllProperties(): array
    {
        $properties = static::PROPERTIES;
        foreach (class_parents($this) as $parentClass) {
            $properties = array_merge($properties, $parentClass::PROPERTIES);
        }

        return $properties;
    }

    private function checkAttribute(string $name): void
    {
        if (!array_key_exists($name, $this->attributes)) {
            throw new OutOfBoundsException(sprintf('%s : Invalid attribute "%s"', static::class, $name));
        }
    }
}
