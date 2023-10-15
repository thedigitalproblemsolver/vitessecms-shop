<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Models;

use VitesseCms\Database\AbstractCollection;
use VitesseCms\Shop\Interfaces\TaxRateInterface;

class TaxRate extends AbstractCollection implements TaxRateInterface
{
    public int $taxrate;

    public function getNameField(?string $languageShort = null): string
    {
        return $this->getName() . '%';
    }

    public function getName(?string $languageShort = null): string
    {
        return (string)$this->taxrate;
    }

    public function getTaxRate(): int
    {
        return (int)$this->taxrate;
    }
}
