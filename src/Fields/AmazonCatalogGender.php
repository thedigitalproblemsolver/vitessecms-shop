<?php declare(strict_types=1);

namespace VitesseCms\Shop\Fields;

use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Datafield\AbstractField;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;

class AmazonCatalogGender extends AbstractField
{
    public function buildItemFormElement(
        AbstractForm $form,
        Datafield $datafield,
        Attributes $attributes,
        AbstractCollection $data = null
    )
    {
        $form->addDropdown(
            'Amazon gender',
            'AmazonCatalogGender',
            (new Attributes())->setOptions(ElementHelper::arrayToSelectOptions([
                'Women' => 'Women',
                'Men' => 'Men',
            ]))
        );
    }
}
