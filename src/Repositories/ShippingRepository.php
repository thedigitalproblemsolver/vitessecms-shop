<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Repositories;

use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Database\Traits\TraitRepositoryConstructor;
use VitesseCms\Database\Traits\TraitRepositoryParseFindAll;
use VitesseCms\Database\Traits\TraitRepositoryParseGetById;
use VitesseCms\Shop\AbstractShippingType;
use VitesseCms\Shop\Models\ShippingIterator;

class ShippingRepository
{
    use TraitRepositoryParseGetById;
    use TraitRepositoryParseFindAll;
    use TraitRepositoryConstructor;

    public function getById(string $id, bool $hideUnpublished = true): ?AbstractShippingType
    {
        return $this->parseGetById($id, $hideUnpublished);
    }

    public function findAll(
        ?FindValueIterator $findValueIterator = null,
        bool $hideUnpublished = true
    ): ShippingIterator {
        return $this->parseFindAll($findValueIterator, $hideUnpublished);
    }
}
