<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Repositories;

use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Database\Traits\TraitRepositoryParseFindAll;
use VitesseCms\Shop\Models\Shopper;
use VitesseCms\Shop\Models\ShopperIterator;

class ShopperRepository
{
    use TraitRepositoryParseFindAll;

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

    public function findAll(
        ?FindValueIterator $findValuesIterator = null,
        bool $hideUnpublished = true
    ): ShopperIterator {
        return $this->parseFindAll($findValuesIterator, $hideUnpublished);
    }
}
