<?php
declare(strict_types=1);

namespace VitesseCms\Shop\Controllers;

use VitesseCms\Core\AbstractController;
use VitesseCms\Shop\Helpers\OrderHelper;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Models\OrderState;
use VitesseCms\Shop\Models\Payment;

class PaymentController extends AbstractController
{
    public function processAction(): void
    {
        $doRedirect = true;

        if ($this->dispatcher->getParam(0) !== null) :
            Order::setFindPublished(false);
            /** @var Order $order */
            $order = Order::findById($this->dispatcher->getParam(0));
            if ($order) :
                $paymentType = $this->processPayment($order);
                $doRedirect = $paymentType->isProcessRedirect();
            else :
                $this->flash->setError('SHOP_ORDER_NOT_FOUND');
            endif;
        endif;

        if ($doRedirect) :
            $this->redirect($this->shop->checkout->getStep(5)->_('slug'));
        else :
            $this->disableView();
        endif;
    }

    protected function processPayment(Order $order): Payment
    {
        $this->session->set('currentOrderId', $order->getId());
        Payment::setFindPublished(false);
        /** @var Payment $paymentType */
        $paymentType = Payment::findById($order->_('paymentType')['_id']);
        $paymentType->prepareOrder($order);
        $orderState = $paymentType->getTransactionState(
            $order->_('paymentType')['transactionId'],
            (string)$order->_('orderState')['_id']
        );
        OrderHelper::setOrderState($order, $orderState);
        $order->set('published', true);
        $order->save();

        $shopOrder = $this->view->renderTemplate(
            'order',
            'partials/shop',
            [
                'orderItem' => $order,
                'shopOrderId' => $order->_('orderId')
            ]
        );
        $this->view->set('shopOrder', $shopOrder);

        if ($orderState->_('messageType')) :
            $messageType = 'set' . ucfirst($orderState->_('messageType'));
            $this->flash->$messageType($orderState->_('messageText'));
        endif;

        $this->log->write(
            $order->getId(),
            Order::class,
            'Order ' . $order->_('orderId') . ' processed with orderstate ' . $orderState->_('calling_name')
        );

        return $paymentType;
    }

    public function redirectAction(): void
    {
        if ($this->dispatcher->getParam(0) !== null) :
            Order::setFindPublished(false);
            /** @var Order $order */
            $order = Order::findById($this->dispatcher->getParam(0));
            if ($order) :
                $this->processPayment($order);
                $this->redirect($this->shop->checkout->getStep(5)->_('slug') . '?v=' . time());
            else :
                $this->flash->setError('SHOP_ORDER_NOT_FOUND');
                $this->redirect('/');
            endif;
        endif;
    }

    public function cancelAction(): void
    {
        $hasErrors = true;

        if ($this->dispatcher->getParam(0) !== null) :
            Order::setFindPublished(false);
            $order = Order::findById($this->dispatcher->getParam(0));
            if ($order) :
                OrderState::setFindValue('short', 'X');
                $order->set('orderState', OrderState::findFirst());
                $order->set('published', false);
                $order->save();
                $hasErrors = false;

                $this->flash->setSucces('SHOP_ORDER_CANCELLED');
            endif;
        endif;

        if ($hasErrors) :
            $this->flash->setError('SHOP_ORDER_NOT_FOUND');
        endif;

        $this->redirect($this->shop->checkout->getStep()->_('slug'));
    }
}
