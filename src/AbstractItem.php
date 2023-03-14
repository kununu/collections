<?php
declare(strict_types=1);

namespace Kununu\Collection;

use BadMethodCallException;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use OutOfBoundsException;

abstract class AbstractItem
{
    protected const SETTER_PREFIX = 'set';
    protected const GETTER_PREFIX = 'get';
    protected const PROPERTIES = [];

    private const DATE_FORMAT = 'Y-m-d H:i:s';

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

    protected static function buildStringGetter(string $fieldName, ?string $default = null): callable
    {
        return fn(array $data): ?string => isset($data[$fieldName]) ? (string) $data[$fieldName] : $default;
    }

    protected static function buildRequiredStringGetter(string $fieldName): callable
    {
        return self::buildGetterRequiredField($fieldName, fn($value): string => (string) $value);
    }

    protected static function buildBoolGetter(string $fieldName, ?bool $default = null): callable
    {
        return fn(array $data): ?bool => isset($data[$fieldName]) ? (bool) $data[$fieldName] : $default;
    }

    protected static function buildRequiredBoolGetter(string $fieldName): callable
    {
        return self::buildGetterRequiredField($fieldName, fn($value): bool => (bool) $value);
    }

    protected static function buildIntGetter(string $fieldName, ?int $default = null): callable
    {
        return fn(array $data): ?int => isset($data[$fieldName]) ? (int) $data[$fieldName] : $default;
    }

    protected static function buildRequiredIntGetter(string $fieldName): callable
    {
        return self::buildGetterRequiredField($fieldName, fn($value): int => (int) $value);
    }

    protected static function buildFloatGetter(string $fieldName, ?float $default = null): callable
    {
        return fn(array $data): ?float => isset($data[$fieldName]) ? (float) $data[$fieldName] : $default;
    }

    protected static function buildRequiredFloatGetter(string $fieldName): callable
    {
        return self::buildGetterRequiredField($fieldName, fn($value): float => (float) $value);
    }

    protected static function buildDateTimeGetter(
        string $fieldName,
        string $dateFormat = self::DATE_FORMAT,
        ?DateTimeInterface $default = null
    ): callable
    {
        return fn(array $data): ?DateTimeInterface => isset($data[$fieldName])
            ? (DateTime::createFromFormat($dateFormat, $data[$fieldName]) ?: $default)
            : null;
    }

    protected static function buildRequiredDateTimeGetter(
        string $fieldName,
        string $dateFormat = self::DATE_FORMAT
    ): callable
    {
        return self::buildGetterRequiredField(
            $fieldName,
            fn($value): DateTimeInterface => DateTime::createFromFormat($dateFormat, $value)
        );
    }

    protected static function buildGetterRequiredField(string $fieldName, callable $converter): callable
    {
        return fn(array $data) => match (true) {
            isset($data[$fieldName]) => $converter($data[$fieldName]),
            default                  => throw new InvalidArgumentException(sprintf('Missing "%s" field', $fieldName))
        };
    }

    protected static function buildCollectionGetter(
        string $fieldName,
        string $collectionClass,
        ?AbstractCollection $default = null
    ): callable
    {
        return fn(array $data): ?AbstractCollection => match (true) {
            !is_a($collectionClass, AbstractCollection::class, true) => null,
            isset($data[$fieldName])                                 => call_user_func_array(
                [$collectionClass, 'fromIterable'],
                [$data[$fieldName]]
            ),
            default => $default
        };
    }

    protected static function buildConditionalGetter(string $sourceField, array $sources): callable
    {
        return function(array $data) use ($sourceField, $sources) {
            foreach ($sources as $source => $getter) {
                if ($source === ($data[$sourceField] ?? null)) {
                    return is_callable($getter) ? $getter($data) : null;
                }
            }

            return null;
        };
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
