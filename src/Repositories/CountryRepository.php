<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Repositories;

use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Database\Traits\TraitRepositoryConstructor;
use VitesseCms\Database\Traits\TraitRepositoryParseFindAll;
use VitesseCms\Database\Traits\TraitRepositoryParseGetById;
use VitesseCms\Shop\Models\Country;
use VitesseCms\Shop\Models\CountryIterator;

final class CountryRepository
{
    use TraitRepositoryParseGetById;
    use TraitRepositoryParseFindAll;
    use TraitRepositoryConstructor;

    public function getById(string $id, bool $hideUnpublished = true): ?Country
    {
        return $this->parseGetById($id, $hideUnpublished);
    }

    public function findAll(?FindValueIterator $findValueIterator = null, bool $hideUnpublished = true): CountryIterator
    {
        return $this->parseFindAll($findValueIterator, $hideUnpublished);
    }
}
