<?php declare(strict_types=1);

namespace VitesseCms\Shop\Forms;

use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;

class ShippingBarcodeForm extends AbstractForm
{
    public function initialize(): void
    {
        $this->addText('Barcode', 'barcode', (new Attributes())->setRequired())
            ->addSubmitButton('%CORE_SAVE%');
    }
}
