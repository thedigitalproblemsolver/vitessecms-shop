<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Models;

use VitesseCms\Shop\AbstractShippingType;

final class Shipping extends AbstractShippingType
{
    public ?string $type = null;
    protected $engine;

    public function calculateOrderTotal(Order $order): float
    {
        $this->setEngine();

        return $this->engine->calculateOrderAmount($order) + $this->engine->calculateOrderVat($order);
    }

    public function setEngine(): void
    {
        if ($this->type && class_exists($this->type)) :
            $shipping = clone $this;
            $this->engine = new $this->type();
            $this->engine->set('shipping', $shipping);
        endif;
    }

    public function calculateOrderAmount(Order $order): float
    {
        $this->setEngine();

        return $this->engine->calculateOrderAmount($order);
    }

    public function calculateOrderVat(Order $order): float
    {
        $this->setEngine();

        return $this->engine->calculateOrderVat($order);
    }

    public function calculateCartTotal(array $items): float
    {
        $this->setEngine();

        return $this->engine->calculateCartAmount($items) + $this->engine->calculateCartVat($items);
    }

    public function calculateCartAmount(array $items): float
    {
        $this->setEngine();

        return $this->engine->calculateCartAmount($items);
    }

    public function calculateCartVat(array $items): float
    {
        $this->setEngine();

        return $this->engine->calculateCartVat($items);
    }

    public function getLabelLink(Order $order): string
    {
        $this->setEngine();

        return $this->engine->getLabelLink($order);
    }

    public function getLabel(Order $order, ?string $packageType): ?string
    {
        $this->setEngine();

        return $this->engine->getLabel($order, $packageType);
    }

    public function hasFreeShippingItems(array $items): bool
    {
        return false;
    }

    public function getTrackAndTraceLink(Order $order): string
    {
        $this->setEngine();

        return $this->engine->getTrackAndTraceLink($order);
    }
}
