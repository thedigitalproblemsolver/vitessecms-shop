<?php

declare(strict_types=1);

namespace VitesseCms\Shop\PaymentTypes;

use Phalcon\Di\Di;
use VitesseCms\Shop\AbstractPaymentType;
use VitesseCms\Shop\Enum\PaymentEnum;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Models\Payment;

class NoPayment extends AbstractPaymentType
{
    public function doPayment(Order $order, Payment $payment): void
    {
        Di::getDefault()->get('log')->write(
            $order->getId(),
            Order::class,
            'Order ' . $order->_('orderId') . ' user with no payment redirected to payment process'
        );

        header('Location: ' . Di::getDefault()->get('url')->getBaseUri() . 'shop/payment/process/' . $order->getId());
        die();
    }

    public function getTransactionState($transactionId, Payment $payment): string
    {
        return PaymentEnum::PAID;
    }

    public function isProcessRedirect(): bool
    {
        return true;
    }
}
