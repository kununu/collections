<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\AbstractItemToArray;
use Stringable;

/**
 * @method int          getId()
 * @method ToStringStub getName()
 * @method ToIntStub    getIndustryId()
 * @method Stringable   getStringable()
 * @method bool         getVerified()
 */
abstract class AbstractItemToArrayStubBase extends AbstractItemToArray
{
    protected const array PROPERTIES = [
        'id',
        'name',
        'industryId',
        'stringable',
        'verified',
    ];

    protected static function getBuilders(): array
    {
        return [
            'id'         => self::buildIntGetter('id'),
            'name'       => self::buildGetterRequiredField(
                'name',
                static fn(string $value): ToStringStub => ToStringStub::create(ToIntStub::fromInt(1000), $value)
            ),
            'industryId' => self::buildGetterRequiredField(
                'industryId',
                static fn(int $value): ToIntStub => ToIntStub::fromInt($value)
            ),
            'stringable' => self::buildGetterRequiredField(
                'name',
                static fn(string $value): Stringable => new readonly class($value) implements Stringable {
                    public function __construct(private string $value)
                    {
                    }

                    public function __toString(): string
                    {
                        return $this->value;
                    }
                }
            ),
            'verified'   => self::buildBoolGetter('verified'),
        ];
    }
}
