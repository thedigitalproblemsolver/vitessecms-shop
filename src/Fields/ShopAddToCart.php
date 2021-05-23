<?php declare(strict_types=1);

namespace VitesseCms\Shop\Fields;

use VitesseCms\Database\AbstractCollection;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Datafield\AbstractField;
use VitesseCms\Form\Models\Attributes;

class ShopAddToCart extends AbstractField
{
    public function buildItemFormElement(
        AbstractForm $form,
        Datafield $datafield,
        Attributes $attributes,
        AbstractCollection $data = null
    )
    {
        $form->addToggle(
            $datafield->getNameField(),
            $datafield->getCallingName(),
            $attributes->setChecked()->setReadonly()
        );
    }
}
