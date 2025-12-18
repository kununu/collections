<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\EnumKeyValue\AbstractEnumKeyValue;

/**
 * @method int         getKey1()
 * @method string|null getKey2()
 * @method float|null  getKey3()
 * @method bool        hasKey1()
 * @method bool        hasKey2()
 * @method bool        hasKey3()
 * @method self        removeKey1()
 * @method self        removeKey2()
 * @method self        removeKey3()
 * @method self        setKey1(int $key1)
 * @method self        setKey2(string|null $key2)
 * @method self        setKey3(float|null $key3)
 */
final class EnumKeyValueStub extends AbstractEnumKeyValue
{
    protected static function createKeyFromString(string $key): EnumStub
    {
        return EnumStub::fromName($key);
    }
}
