<?php declare(strict_types=1);

namespace VitesseCms\Shop\Factories;

use VitesseCms\Shop\Models\TaxRate;

class TaxrateFactory
{
    public static function create(string $name, float $taxrate, bool $published = false): TaxRate
    {
        return (new TaxRate())
            ->set('name', $name, true)
            ->set('taxrate', $taxrate)
            ->set('published', $published);
    }
}
