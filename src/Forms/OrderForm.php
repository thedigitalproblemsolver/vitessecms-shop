<?php declare(strict_types=1);

namespace VitesseCms\Shop\Forms;

use VitesseCms\Form\AbstractForm;
use VitesseCms\Shop\Models\Order;

class OrderForm extends AbstractForm
{
    public function initialize(Order $item = null)
    {
        $this->addText('Orderid', 'orderId')
            ->addText('subTotal', 'subTotal')
            ->addText('tax', 'tax')
            ->addText('total', 'total')
            ->addText('shippingAmount', 'shippingAmount')
            ->addText('shippingTax', 'shippingTax')
            ->addText('orderState', 'orderState');
    }
}
