<?php declare(strict_types=1);

namespace VitesseCms\Shop\Helpers;

use VitesseCms\Shop\Interfaces\TaxRateInterface;

class TaxrateHelper
{
    public static function calculateExVatPrice(
        TaxRateInterface $taxrate,
        float $price
    ): float {
        $taxBase = 100 +  $taxrate->_('taxrate');

        return ( $price / $taxBase ) * 100;
    }
}
