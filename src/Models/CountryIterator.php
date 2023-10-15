<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Models;

use ArrayIterator;

class CountryIterator extends ArrayIterator
{
    public function __construct(array $countries)
    {
        parent::__construct($countries);
    }

    public function current(): Country
    {
        return parent::current();
    }
}
