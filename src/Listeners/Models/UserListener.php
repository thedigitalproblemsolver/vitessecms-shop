<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Models;

use ArrayIterator;
use Phalcon\Events\Event;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Log\Services\LogService;
use VitesseCms\Shop\Repositories\OrderRepository;
use VitesseCms\Shop\Repositories\ShipToAddressRepository;
use VitesseCms\Shop\Repositories\ShopperRepository;
use VitesseCms\User\Models\User;

class UserListener
{
    public function __construct(
        private readonly LogService $logService,
        private readonly ShopperRepository $shopperRepository,
        private readonly ShipToAddressRepository $shipToAddressRepository,
        private readonly OrderRepository $orderRepository
    ) {
        //overige
        //delete form submissions
        //delete newsletter stuff
        //
    }

    public function beforeDelete(Event $event, User $user): bool
    {
        $this->deleteOrders((string)$user->getId());
        $this->deleteShopper((string)$user->getId());
        $this->deleteShipToAddress((string)$user->getId());

        return true;
    }

    private function deleteOrders(string $userId): void
    {
        $this->performDeletion(
            $this->orderRepository->findAll(new FindValueIterator([new FindValue('shopper.userId', $userId)])),
            'Order',
            'Orders'
        );
    }

    private function performDeletion(ArrayIterator $models, string $type, string $types): void
    {
        if ($models->count() > 0) {
            $this->logService->message('Start deleting ' . $types);
            while ($models->valid()) {
                if ($models->current()->delete()) {
                    $this->logService->message('Deleted a ' . $type);
                } else {
                    $this->logService->message('Failed to delete a ' . $type);
                }

                $models->next();
            }
            $this->logService->message('Finished deleting ' . $types);
        } else {
            $this->logService->message('No ' . $types . ' found to delete');
        }
    }

    private function deleteShopper(string $userId): void
    {
        $this->performDeletion(
            $this->shopperRepository->findAll(new FindValueIterator([new FindValue('userId', $userId)])),
            'Shopper',
            'Shoppers'
        );
    }

    private function deleteShipToAddress(string $userId): void
    {
        $this->performDeletion(
            $this->shipToAddressRepository->findAll(new FindValueIterator([new FindValue('userId', $userId)])),
            'ShipTo Address',
            'ShipTo Addresses'
        );
    }
}