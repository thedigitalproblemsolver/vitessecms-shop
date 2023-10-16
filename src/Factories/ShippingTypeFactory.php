<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Factories;

use VitesseCms\Shop\AbstractShippingType;

final class ShippingTypeFactory
{
    public static function createFromArray(array $data): AbstractShippingType
    {
        /** @var AbstractShippingType $shippingType */
        $shippingType = new $data['type']();
        foreach ($data as $key => $value) :
            $shippingType->set($key, $value);
        endforeach;

        return $shippingType;
    }
}
