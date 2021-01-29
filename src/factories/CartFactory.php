<?php declare(strict_types=1);

namespace VitesseCms\Shop\Factories;

use VitesseCms\Core\AbstractFactory;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Core\Interfaces\FactoryInterface;
use VitesseCms\Shop\Models\Cart;

class CartFactory extends AbstractFactory implements FactoryInterface
{
    public static function create(BaseObjectInterface $bindData = null) : BaseObjectInterface
    {
        $cart = parent::createCollection(Cart::class);

        return $cart;
    }
}
