<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Models;

use VitesseCms\Shop\Repositories\OrderStateRepository;

final class OrderStateListener
{
    public function __construct(private readonly OrderStateRepository $orderStateRepository)
    {
    }

    public function getRepository(): OrderStateRepository
    {
        return $this->orderStateRepository;
    }
}