<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Models;

use ArrayIterator as ArrayIteratorAlias;

class OrderIterator extends ArrayIteratorAlias
{
    public function __construct(array $orders)
    {
        parent::__construct($orders);
    }

    public function current(): Order
    {
        return parent::current();
    }
}
