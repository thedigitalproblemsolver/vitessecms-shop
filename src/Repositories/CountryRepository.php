<?php
declare(strict_types=1);

namespace VitesseCms\Shop\Repositories;

class CountryRepository extends AbstractCollectionRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new Country();
    }
}
