<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use DateTime;
use DateTimeImmutable;
use Kununu\Collection\AbstractItem;

/**
 * @method string|null            stringField()
 * @method string                 requiredStringField()
 * @method bool|null              boolField()
 * @method bool                   requiredBoolField()
 * @method int|null               intField()
 * @method int                    requiredIntField()
 * @method float|null             floatField()
 * @method float                  requiredFloatField()
 * @method DateTime|null          dateTimeField()
 * @method DateTime               requiredDateTimeField()
 * @method DateTimeImmutable|null dateTimeImmutableField()
 * @method DateTimeImmutable      requiredDateTimeImmutableField()
 */
final class AbstractItemSnakeCaseStub extends AbstractItem
{
    protected const string GETTER_PREFIX = '';

    protected const array PROPERTIES = [
        'stringField',
        'requiredStringField',
        'boolField',
        'requiredBoolField',
        'intField',
        'requiredIntField',
        'floatField',
        'requiredFloatField',
        'dateTimeField',
        'requiredDateTimeField',
        'dateTimeImmutableField',
        'requiredDateTimeImmutableField',
    ];

    protected static function getBuilders(): array
    {
        return [
            'stringField'                    => self::buildStringGetter('stringField', useSnakeCase: true),
            'requiredStringField'            => self::buildRequiredStringGetter('requiredStringField', true),
            'boolField'                      => self::buildBoolGetter('boolField', useSnakeCase: true),
            'requiredBoolField'              => self::buildRequiredBoolGetter('requiredBoolField', true),
            'intField'                       => self::buildIntGetter('intField', useSnakeCase: true),
            'requiredIntField'               => self::buildRequiredIntGetter('requiredIntField', true),
            'floatField'                     => self::buildFloatGetter('floatField', useSnakeCase: true),
            'requiredFloatField'             => self::buildRequiredFloatGetter('requiredFloatField', true),
            'dateTimeField'                  => self::buildDateTimeGetter('dateTimeField', useSnakeCase: true),
            'requiredDateTimeField'          => self::buildRequiredDateTimeGetter(
                'requiredDateTimeField',
                useSnakeCase: true
            ),
            'dateTimeImmutableField'         => self::buildDateTimeImmutableGetter(
                'dateTimeImmutableField',
                useSnakeCase: true
            ),
            'requiredDateTimeImmutableField' => self::buildRequiredDateTimeImmutableGetter(
                'requiredDateTimeImmutableField',
                useSnakeCase: true
            ),
        ];
    }
}
