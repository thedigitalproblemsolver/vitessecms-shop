<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Models;

use ArrayIterator;

class ShopperIterator extends ArrayIterator
{
    public function __construct(array $shoppers)
    {
        parent::__construct($shoppers);
    }

    public function current(): Shopper
    {
        return parent::current();
    }
}
