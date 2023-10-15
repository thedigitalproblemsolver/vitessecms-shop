<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Repositories;

use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Database\Traits\TraitRepositoryConstructor;
use VitesseCms\Database\Traits\TraitRepositoryParseFindAll;
use VitesseCms\Database\Traits\TraitRepositoryParseGetById;
use VitesseCms\Shop\Models\TaxRate;
use VitesseCms\Shop\Models\TaxRateIterator;

final class TaxRateRepository
{
    use TraitRepositoryParseGetById;
    use TraitRepositoryParseFindAll;
    use TraitRepositoryConstructor;

    public function getById(string $id, bool $hideUnpublished = true): ?TaxRate
    {
        return $this->parseGetById($id, $hideUnpublished);
    }

    public function findAll(?FindValueIterator $findValueIterator = null, bool $hideUnpublished = true): TaxRateIterator
    {
        return $this->parseFindAll($findValueIterator, $hideUnpublished);
    }
}
