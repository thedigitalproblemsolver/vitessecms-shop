<?php declare(strict_types=1);

namespace VitesseCms\Shop\Factories;

use MyParcelNL\Sdk\src\Model\MyParcelCustomsItem;
use VitesseCms\Shop\Models\Country;
use VitesseCms\Shop\Models\Order;

class MyParcelCustomsItemFactory
{
    public static function createFromOrder(Order $order): MyParcelCustomsItem
    {
        //TODO handle weight throught Item and information from spreadshirts productType
        $weight = 200 * count($order->getProducts());
        $shiptoAddress = ShiptoAddressFactory::createFromOrderArray($order->getShiptoAddress());
        /** @var Country $country */
        $country = Country::findById($shiptoAddress->getCountryId());

        return (new MyParcelCustomsItem())
            ->setDescription((string)$order->getNumber())
            ->setAmount(1)
            ->setWeight($weight)
            ->setItemValue($order->getTotal())
            ->setClassification(1410)
            ->setCountry($country->getShort());
    }
}
