# AutoSortableOffsetSetTrait

This trait will provide a basic rewrite of `offsetSet` to your `ArrayIterator` based collections.

By doing a proper implementation of the `append` method you can auto-sort your collections when appending an item.

## Example

When appending to an instance of this collection it will automatically sort it (and in this example also only allow one entry per key).

```php
<?php
declare(strict_types=1);

use Kununu\Collection\AbstractCollection;
use Kununu\Collection\AutoSortableOffsetSetTrait;

final class MyCollection extends AbstractCollection
{
    use AutoSortableOffsetSetTrait;

    public function append($value): void
    {
        match (true) {
            is_int($value) => $this->offsetSet($value, $value),
            default        => parent::append($value)
        };
    }
}

$collection = new MyCollection();

$collection->append(10);
$collection->append(3);
$collection->append(1);
$collection->append(7);

var_export($collection->toArray()); // Will dump [1, 3, 7, 10]
```

---

[Back to Index](../README.md)
