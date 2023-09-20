<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Repositories;

use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Shop\Models\Shopper;
use VitesseCms\Shop\Models\ShopperIterator;

class ShopperRepository
{
    public function getByUserid(string $userId): ?Shopper
    {
        Shopper::setFindValue('userId', $userId);
        /** @var Shopper $shopper */
        $shopper = Shopper::findFirst();

        if ($shopper instanceof Shopper):
            return $shopper;
        endif;

        return null;
    }

    public function findAll(?FindValueIterator $findValues = null, bool $hideUnpublished = true): ShopperIterator
    {
        Shopper::setFindPublished($hideUnpublished);
        Shopper::addFindOrder('name');
        $this->parseFindValues($findValues);

        return new ShopperIterator(Shopper::findAll());
    }

    protected function parseFindValues(?FindValueIterator $findValues = null): void
    {
        if ($findValues !== null) :
            while ($findValues->valid()) :
                $findValue = $findValues->current();
                Shopper::setFindValue(
                    $findValue->getKey(),
                    $findValue->getValue(),
                    $findValue->getType()
                );
                $findValues->next();
            endwhile;
        endif;
    }
}
