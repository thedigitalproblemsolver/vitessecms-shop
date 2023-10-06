<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Repositories;

use VitesseCms\Shop\Models\Country;

final class CountryRepository
{
    public function getById(string $id, bool $hideUnpublished = true): ?Country
    {
        Country::setFindPublished($hideUnpublished);

        /** @var Country $country */
        $country = Country::findById($id);
        if (is_object($country)):
            return $country;
        endif;

        return null;
    }
}
