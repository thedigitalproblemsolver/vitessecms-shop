<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Blocks;

use MongoDB\BSON\ObjectID;
use VitesseCms\Block\AbstractBlockModel;
use VitesseCms\Block\Models\Block;
use VitesseCms\Core\Factories\PaginationFactory;
use VitesseCms\Shop\Models\Order;

class ShopUserOrders extends AbstractBlockModel
{
    public function initialize()
    {
        parent::initialize();

        $this->excludeFromCache = true;
    }

    public function parse(Block $block): void
    {
        parent::parse($block);

        if ($this->getDi()->get('user')->isLoggedIn()) :
            Order::setFindPublished(false);
            Order::setFindValue(
                'shopper.user._id',
                new ObjectID((string)$this->getDi()->get('user')->getId())
            );
            Order::addFindOrder('orderId', -1);
            $orders = Order::findAll();
            $pagination = PaginationFactory::createFromArray(
                $orders,
                $this->getDi()->get('request'),
                $this->getDi()->get('url')
            );

            $orderList = $this->view->renderTemplate(
                'affiliate_orderlist',
                $this->getDi()->get('configuration')->getRootDir() . 'Template/core/Views/partials/shop',
                [
                    'orderlistOrders' => $pagination->items,
                    'pagination' => $pagination
                ]
            );
            $block->set('orderList', $orderList);
        endif;
    }
}
