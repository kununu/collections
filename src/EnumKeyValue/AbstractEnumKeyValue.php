<?php
declare(strict_types=1);

namespace Kununu\Collection\EnumKeyValue;

use BadMethodCallException;
use Kununu\Collection\EnumKeyValue\Exception\RemovingRequiredKeyException;
use Kununu\Collection\EnumKeyValue\Exception\RequiredKeyMissingException;
use Kununu\Collection\Helper\FormatOption;
use Kununu\Collection\Helper\MethodsHelperTrait;
use Kununu\Collection\KeyValueInterface;
use Kununu\Collection\KeyValueTrait;

abstract class AbstractEnumKeyValue implements KeyValueInterface
{
    use KeyValueTrait;
    use MethodsHelperTrait;

    protected const string HAS_PREFIX = 'has';
    protected const string SETTER_PREFIX = 'set';
    protected const string GETTER_PREFIX = 'get';
    protected const string REMOVE_PREFIX = 'remove';
    protected const FormatOption FORMAT_OPTION = FormatOption::UpperCaseFirst;

    public function __call(string $method, array $args)
    {
        return match (true) {
            $this->matches($method, static::GETTER_PREFIX) => $this->get(
                $this->getKeyForMethod($method, static::GETTER_PREFIX)
            ),
            $this->matches($method, static::HAS_PREFIX)    => $this->has(
                $this->getKeyForMethod($method, static::HAS_PREFIX)
            ),
            $this->matches($method, static::REMOVE_PREFIX) => $this->remove(
                $this->getKeyForMethod($method, static::REMOVE_PREFIX),
            ),
            $this->matches($method, static::SETTER_PREFIX) => $this->set(
                $this->getKeyForMethod($method, static::SETTER_PREFIX),
                current($args)
            ),
            default                                        => $this->throwBadMethodCallException($method),
        };
    }

    /** @throws RequiredKeyMissingException */
    public function get(string|EnumKeyInterface $key, mixed $default = null): mixed
    {
        $key = self::createKey($key);

        if ($key->required() && !$this->has($key)) {
            throw new RequiredKeyMissingException($key);
        }

        return $this->has($key) ? $this->values[$key->key()] : $default;
    }

    public function has(string|EnumKeyInterface $key): bool
    {
        return array_key_exists(self::createKey($key)->key(), $this->values);
    }

    public function keys(bool $asString = true): array
    {
        return match ($asString) {
            false => array_map(static::createKey(...), array_keys($this->values)),
            true  => array_keys($this->values),
        };
    }

    /** @throws RemovingRequiredKeyException */
    public function remove(string|EnumKeyInterface $key): self
    {
        $key = self::createKey($key);

        if ($key->required() && $this->has($key)) {
            throw new RemovingRequiredKeyException($key);
        }

        unset($this->values[self::createKey($key)->key()]);

        return $this;
    }

    public function set(string|EnumKeyInterface $key, mixed $value): self
    {
        $this->values[self::createKey($key)->key()] = $value;

        return $this;
    }

    abstract protected static function createKeyFromString(string $key): EnumKeyInterface;

    private static function createKey(string|EnumKeyInterface $key): EnumKeyInterface
    {
        return match (true) {
            $key instanceof EnumKeyInterface => $key,
            default                          => static::createKeyFromString($key),
        };
    }

    private function getKeyForMethod(string $method, string $prefix): EnumKeyInterface
    {
        return static::createKey($this->fromMethodName($method, $prefix, static::FORMAT_OPTION));
    }

    private function throwBadMethodCallException(string $method): void
    {
        throw new BadMethodCallException(sprintf('%s: Invalid method "%s" called', static::class, $method));
    }
}
