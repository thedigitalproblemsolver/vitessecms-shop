<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Models;

use ArrayIterator as ArrayIteratorAlias;

class ShipToAddressIterator extends ArrayIteratorAlias
{
    public function __construct(array $shipToAddresses)
    {
        parent::__construct($shipToAddresses);
    }

    public function current(): ShiptoAddress
    {
        return parent::current();
    }
}
