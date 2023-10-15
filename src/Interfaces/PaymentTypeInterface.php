<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Interfaces;

use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Models\Payment;

interface PaymentTypeInterface
{
    public function doPayment(Order $order, Payment $payment): void;

    public function getTransactionState(string $transactionId, Payment $payment): string;

    public function prepareOrder(Order $order): void;

    public function isProcessRedirect(): bool;
}
