<?php declare(strict_types=1);

namespace VitesseCms\Shop\Forms;

use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\Models\TaxRate;


class TaxRateForm extends AbstractForm
{
    public function initialize( TaxRate $item = null)
    {
        $this->addNumber(
            '%ADMIN_TAX_RATE%',
            'taxrate',
            (new Attributes())->setRequired()
                ->setMin(0)
                ->setMax(100)
            )
            ->addSubmitButton('%CORE_SAVE%')
        ;
    }
}
