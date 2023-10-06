<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Models;

use VitesseCms\Shop\Repositories\CountryRepository;

final class CountryListener
{
    public function __construct(private readonly CountryRepository $countryRepository)
    {
    }

    public function getRepository(): CountryRepository
    {
        return $this->countryRepository;
    }
}