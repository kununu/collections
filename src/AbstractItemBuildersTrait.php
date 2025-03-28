<?php
declare(strict_types=1);

namespace Kununu\Collection;

use Kununu\Collection\AbstractItemBuilderTraits\BooleanBuildersTrait;
use Kununu\Collection\AbstractItemBuilderTraits\CollectionBuildersTrait;
use Kununu\Collection\AbstractItemBuilderTraits\ConverterBuildersTrait;
use Kununu\Collection\AbstractItemBuilderTraits\DateTimeBuildersTrait;
use Kununu\Collection\AbstractItemBuilderTraits\FloatBuildersTrait;
use Kununu\Collection\AbstractItemBuilderTraits\FromArrayBuildersTrait;
use Kununu\Collection\AbstractItemBuilderTraits\GenericBuildersTrait;
use Kununu\Collection\AbstractItemBuilderTraits\IntegerBuildersTrait;
use Kununu\Collection\AbstractItemBuilderTraits\StringBuildersTrait;

trait AbstractItemBuildersTrait
{
    use BooleanBuildersTrait;
    use CollectionBuildersTrait;
    use ConverterBuildersTrait;
    use DateTimeBuildersTrait;
    use FloatBuildersTrait;
    use FromArrayBuildersTrait;
    use GenericBuildersTrait;
    use IntegerBuildersTrait;
    use StringBuildersTrait;
}
