<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Stub;

use Kununu\Collection\AbstractItem;

/**
 * @method int|float|string value()
 */
final class AbstractItemWithConditionalBuilderStub extends AbstractItem
{
    protected const string GETTER_PREFIX = '';

    protected const array PROPERTIES = [
        'value',
    ];

    protected static function getBuilders(): array
    {
        return [
            'value' => self::buildConditionalGetter(
                'source',
                [
                    'int'    => self::buildRequiredIntGetter('value'),
                    'float'  => self::buildRequiredFloatGetter('value'),
                    'string' => self::buildRequiredStringGetter('value'),
                ]
            ),
        ];
    }
}
