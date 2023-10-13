<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Models;

use VitesseCms\Database\Traits\TraitRepositoryListener;
use VitesseCms\Shop\Repositories\PaymentRepository;

final class PaymentListener
{
    use TraitRepositoryListener;

    public function __construct(private readonly string $class)
    {
        $this->setRepositoryClass($this->class);
    }

    public function getRepository(): PaymentRepository
    {
        return $this->parseGetRepository();
    }
}