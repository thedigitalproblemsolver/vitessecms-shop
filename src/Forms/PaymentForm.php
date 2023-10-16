<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Forms;

use VitesseCms\Admin\Interfaces\AdminModelFormInterface;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\AbstractPaymentType;
use VitesseCms\Shop\Models\Payment;

final class PaymentForm extends AbstractForm implements AdminModelFormInterface
{
    public function buildForm(): void
    {
        if ($this->entity === null) :
            $this->entity = new Payment();
            $this->entity->set('type', null);
        endif;

        $this->addText('%CORE_NAME%', 'name', (new Attributes())->setRequired(true)->setMultilang(true));

        if (!$this->entity->_('type')) :
            $this->addDropdown(
                '%ADMIN_TYPE%',
                'type',
                (new Attributes())
                    ->setRequired(true)
                    ->setOptions(ElementHelper::arrayToSelectOptions((new Payment())->getTypes()))
            );
        else :
            $object = $this->entity->getTypeClass();
            /** @var AbstractPaymentType $paymentType */
            $paymentType = new $object();
            $paymentType->buildAdminForm($this);
        endif;

        $this->addSubmitButton('%CORE_SAVE%');
    }
}
