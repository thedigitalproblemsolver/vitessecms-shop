<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Forms;

use VitesseCms\Admin\Interfaces\AdminModelFormInterface;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Shop\Models\Order;

final class OrderForm extends AbstractForm implements AdminModelFormInterface
{
    public function buildForm(Order $item = null): void
    {
    }
}
