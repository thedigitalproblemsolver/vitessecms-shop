<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Models;

use VitesseCms\Shop\Repositories\OrderRepository;

final class OrderListener
{
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }

    public function getRepository(): OrderRepository
    {
        return $this->orderRepository;
    }
}