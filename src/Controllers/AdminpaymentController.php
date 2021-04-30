<?php declare(strict_types=1);

namespace VitesseCms\Shop\Controllers;

use VitesseCms\Admin\AbstractAdminController;
use VitesseCms\Shop\Forms\PaymentForm;
use VitesseCms\Shop\Models\Payment;

class AdminpaymentController extends AbstractAdminController
{
    public function onConstruct()
    {
        parent::onConstruct();

        $this->class = Payment::class;
        $this->classForm = PaymentForm::class;
    }
}
