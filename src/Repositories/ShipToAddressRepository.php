<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Repositories;

use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Shop\Models\ShiptoAddress;
use VitesseCms\Shop\Models\ShipToAddressIterator;

class ShipToAddressRepository
{
    public function findAll(?FindValueIterator $findValues = null, bool $hideUnpublished = true): ShipToAddressIterator
    {
        ShiptoAddress::setFindPublished($hideUnpublished);
        ShiptoAddress::addFindOrder('name');
        $this->parseFindValues($findValues);

        return new ShipToAddressIterator(ShiptoAddress::findAll());
    }

    protected function parseFindValues(?FindValueIterator $findValues = null): void
    {
        if ($findValues !== null) :
            while ($findValues->valid()) :
                $findValue = $findValues->current();
                ShiptoAddress::setFindValue(
                    $findValue->getKey(),
                    $findValue->getValue(),
                    $findValue->getType()
                );
                $findValues->next();
            endwhile;
        endif;
    }
}
