<?php
declare(strict_types=1);

namespace Kununu\Collection;

use BadMethodCallException;
use DateTime;
use InvalidArgumentException;
use OutOfBoundsException;

abstract class AbstractItem
{
    protected const SETTER_PREFIX = 'set';
    protected const GETTER_PREFIX = 'get';

    protected const PROPERTIES = [];

    private const DATE_FORMAT = 'Y-m-d H:i:s';

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
                throw new BadMethodCallException(sprintf('%s: Invalid method "%s" called', static::class, $method));
        }

        return $set ? $this->setAttribute($attribute, $value) : $this->getAttribute($attribute);
    }

    protected static function buildStringGetter(string $fieldName, ?string $default = null): callable
    {
        return function(array $data) use ($fieldName, $default): ?string {
            return isset($data[$fieldName]) ? (string) $data[$fieldName] : $default;
        };
    }

    protected static function buildRequiredStringGetter(string $fieldName): callable
    {
        return self::buildGetterRequiredField(
            $fieldName,
            function($value): string {
                return (string) $value;
            }
        );
    }

    protected static function buildBoolGetter(string $fieldName, ?bool $default = null): callable
    {
        return function(array $data) use ($fieldName, $default): ?bool {
            return isset($data[$fieldName]) ? (bool) $data[$fieldName] : $default;
        };
    }

    protected static function buildRequiredBoolGetter(string $fieldName): callable
    {
        return self::buildGetterRequiredField(
            $fieldName,
            function($value): bool {
                return (bool) $value;
            }
        );
    }

    protected static function buildIntGetter(string $fieldName, ?int $default): callable
    {
        return function(array $data) use ($fieldName, $default): ?int {
            return isset($data[$fieldName]) ? (int) $data[$fieldName] : $default;
        };
    }

    protected static function buildRequiredIntGetter(string $fieldName): callable
    {
        return self::buildGetterRequiredField(
            $fieldName,
            function($value): int {
                return (int) $value;
            }
        );
    }

    protected static function buildDateTimeGetter(
        string $fieldName,
        string $dateFormat = self::DATE_FORMAT,
        ?DateTime $default = null
    ): callable {
        return function(array $data) use ($fieldName, $dateFormat, $default): ?DateTime {
            if (isset($data[$fieldName])) {
                return DateTime::createFromFormat($dateFormat, $data[$fieldName]) ?: $default;
            }

            return null;
        };
    }

    protected static function buildRequiredDateTimeGetter(string $fieldName, string $dateFormat = self::DATE_FORMAT): callable
    {
        return self::buildGetterRequiredField(
            $fieldName,
            function($value) use ($dateFormat): DateTime {
                return DateTime::createFromFormat($dateFormat, $value);
            }
        );
    }

    protected static function buildGetterRequiredField(string $fieldName, callable $converter): callable
    {
        return function(array $data) use ($fieldName, $converter) {
            if (!isset($data[$fieldName])) {
                throw new InvalidArgumentException(sprintf('Missing %s field', $fieldName));
            }

            return $converter($data[$fieldName]);
        };
    }

    /**
     * Ready to be rewritten in your subclass!
     *
     * @codeCoverageIgnore
     *
     * @return array
     *               [
     *               'itemProperty' => function(array $data) { return $valueForTheProperty; }
     *               ]
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
            throw new OutOfBoundsException(sprintf('%s : Invalid attribute "%s"', static::class, $name));
        }
    }
}
