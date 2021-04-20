<?php declare(strict_types=1);

namespace VitesseCms\Shop;

use VitesseCms\Core\AbstractInjectable;
use VitesseCms\Core\Interfaces\ExtendAdminFormInterface;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Shop\Interfaces\PaymentTypeInterface;
use VitesseCms\Shop\Models\Order;

abstract class AbstractPaymentType
    extends AbstractInjectable
    implements PaymentTypeInterface, ExtendAdminFormInterface
{
    public function buildAdminForm(AbstractForm $form): void
    {
    }

    public function prepareOrder(Order $order): void
    {
        $paymentType = $order->_('paymentType');
        $paymentType['transactionId'] = (string)$order->_('orderId');
        $order->set('paymentType', $paymentType);
    }

    public function isProcessRedirect(): bool
    {
        return false;
    }
}
