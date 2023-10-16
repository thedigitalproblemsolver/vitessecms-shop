<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Repositories;

use MongoDB\BSON\ObjectId;
use VitesseCms\Database\Models\FindOrder;
use VitesseCms\Database\Models\FindOrderIterator;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Database\Traits\TraitRepositoryConstructor;
use VitesseCms\Database\Traits\TraitRepositoryParseFindAll;
use VitesseCms\Database\Traits\TraitRepositoryParseGetById;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Models\OrderIterator;

class OrderRepository
{
    use TraitRepositoryParseGetById;
    use TraitRepositoryParseFindAll;
    use TraitRepositoryConstructor;

    public function getById(string $id, bool $hideUnpublished = true): ?Order
    {
        return $this->parseGetById($id, $hideUnpublished);
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
        ?FindValueIterator $findValueIterator = null,
        bool $hideUnpublished = true,
        ?int $limit = null,
        ?FindOrderIterator $findOrders = null
    ): OrderIterator {
        if ($findOrders === null):
            $findOrders = new FindOrderIterator([new FindOrder('orderId', -1)]);
        endif;

        return $this->parseFindAll($findValueIterator, $hideUnpublished, $limit, $findOrders);
    }
}
