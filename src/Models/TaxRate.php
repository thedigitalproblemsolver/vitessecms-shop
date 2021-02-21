<?php declare(strict_types=1);

namespace VitesseCms\Shop\Models;

use VitesseCms\Database\AbstractCollection;
use VitesseCms\Shop\Interfaces\TaxRateInterface;

class TaxRate extends AbstractCollection implements TaxRateInterface
{
    /**
     * @var int
     */
    public $taxrate;

    public function getAdminlistName(): string
    {
        return $this->getName() . '%';
    }

    public function getName(?string $languageShort = NULL): string
    {
        return (string)$this->taxrate;
    }

    public function getTaxRate(): int
    {
        return (int)$this->taxrate;
    }
}
