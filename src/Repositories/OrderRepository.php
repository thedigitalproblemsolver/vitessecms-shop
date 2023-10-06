<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Repositories;

use MongoDB\BSON\ObjectId;
use VitesseCms\Database\Models\FindOrder;
use VitesseCms\Database\Models\FindOrderIterator;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Models\OrderIterator;

class OrderRepository
{
    public function getById(string $id, bool $hideUnpublished = true): ?Order
    {
        Order::setFindPublished($hideUnpublished);

        /** @var Order $order */
        $order = Order::findById($id);
        if (is_object($order)):
            return $order;
        endif;

        return null;
    }

    public function getViewOrder(string $id, ObjectId $userId): ?Order
    {
        Order::setFindPublished(false);
        Order::setFindValue('shopper.user._id', $userId);
        /** @var Order $order */
        $order = Order::findById($id);

        if ($order instanceof Order):
            return $order;
        endif;

        return null;
    }

    public function findAll(
        ?FindValueIterator $findValues = null,
        bool $hideUnpublished = true,
        ?int $limit = null,
        ?FindOrderIterator $findOrders = null
    ): OrderIterator {
        Order::setFindPublished($hideUnpublished);
        if ($findOrders === null):
            $findOrders = new FindOrderIterator([new FindOrder('orderId', -1)]);
        endif;

        $this->parseFindValues($findValues);
        $this->parseFindOrders($findOrders);

        return new OrderIterator(Order::findAll());
    }

    protected function parseFindValues(?FindValueIterator $findValues = null): void
    {
        if ($findValues !== null) :
            while ($findValues->valid()) :
                $findValue = $findValues->current();
                Order::setFindValue(
                    $findValue->getKey(),
                    $findValue->getValue(),
                    $findValue->getType()
                );
                $findValues->next();
            endwhile;
        endif;
    }

    protected function parseFindOrders(?FindOrderIterator $findOrders = null): void
    {
        if ($findOrders !== null) :
            while ($findOrders->valid()) :
                $findOrder = $findOrders->current();
                Order::addFindOrder(
                    $findOrder->getKey(),
                    $findOrder->getOrder()
                );
                $findOrders->next();
            endwhile;
        endif;
    }
}
