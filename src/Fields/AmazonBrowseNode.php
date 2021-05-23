<?php declare(strict_types=1);

namespace VitesseCms\Shop\Fields;

use VitesseCms\Database\AbstractCollection;
use VitesseCms\Datafield\Enums\AmazonEnum;
use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Datafield\AbstractField;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;

class FieldAmazonBrowseNode extends AbstractField
{
    public function buildItemFormElement(
        AbstractForm $form,
        Datafield $datafield,
        Attributes $attributes,
        AbstractCollection $data = null
    ) {
        $attributes = new Attributes();
        if ($data !== null) {
            $attributes->setOptions(ElementHelper::arrayToSelectOptions(
                AmazonEnum::nodes,
                [$data->_('AmazonBrowseNode')])
            );
        }

        $form->addDropdown('Amazon Browse Node', 'AmazonBrowseNode', $attributes);
    }
}
