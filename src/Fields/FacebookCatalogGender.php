<?php declare(strict_types=1);

namespace VitesseCms\Shop\Fields;

use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Datafield\AbstractField;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;

class FacebookCatalogGender extends AbstractField
{
    public function buildItemFormElement(
        AbstractForm $form,
        Datafield $datafield,
        Attributes $attributes,
        AbstractCollection $data = null
    )
    {
        $form->addDropdown(
            'Facebook gender',
            'FacebookCatalogGender',
            $attributes->setOptions(ElementHelper::arrayToSelectOptions([
                'female' => 'female',
                'male' => 'male',
                'unisex' => 'unisex',
            ]))
        );
    }
}
