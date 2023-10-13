<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Models;

use ArrayIterator;

class TaxRateIterator extends ArrayIterator
{
    public function __construct(array $taxRates)
    {
        parent::__construct($taxRates);
    }

    public function current(): TaxRate
    {
        return parent::current();
    }
}
