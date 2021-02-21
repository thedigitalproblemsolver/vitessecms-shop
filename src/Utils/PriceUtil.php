<?php declare(strict_types=1);

namespace VitesseCms\Shop\Utils;

class PriceUtil
{
    public static function formatDisplay(float $price): string
    {
        return number_format($price, 2, ',', '.');
    }
}
