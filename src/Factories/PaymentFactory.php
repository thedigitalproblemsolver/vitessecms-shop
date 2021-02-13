<?php declare(strict_types=1);

namespace VitesseCms\Shop\Factories;

use VitesseCms\Shop\Models\Payment;

class PaymentFactory
{
    public static function create(string $name, string $paymentType, bool $published = false): Payment {
        return (new Payment())
            ->set('name', $name, true)
            ->set('type', $paymentType)
            ->set('published', $published);
    }
}
