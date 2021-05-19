<?php declare(strict_types=1);

namespace VitesseCms\Shop\Blocks;

use VitesseCms\Block\AbstractBlockModel;
use VitesseCms\Block\Models\Block;
use VitesseCms\Core\Factories\PaginatonFactory;
use VitesseCms\Shop\Models\Order;
use MongoDB\BSON\ObjectID;

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

        if ($this->di->user->isLoggedIn()) :
            Order::setFindPublished(false);
            Order::setFindValue(
                'shopper.user._id',
                new ObjectID((string)$this->di->user->getId())
            );
            Order::addFindOrder('orderId', -1);
            $orders = Order::findAll();
            $pagination = PaginatonFactory::createFromArray($orders, $this->di->request, $this->di->url);

            $orderList = $this->view->renderTemplate(
                'affiliate_orderlist',
                $this->di->configuration->getRootDir() . 'Template/core/Views/partials/shop',
                [
                    'orderlistOrders' => $pagination->items,
                    'pagination' => $pagination
                ]
            );
            $block->set('orderList', $orderList);
        endif;
    }
}
