<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Forms;

use VitesseCms\Database\AbstractCollection;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\Helpers\ShippingHelper;
use VitesseCms\Shop\Models\Shipping;

final class ShippingForm extends AbstractForm
{
    public function initialize(Shipping $item = null)
    {
        if ($item === null) {
            $item = new Shipping();
        }

        $this->addText('%CORE_NAME%', 'name', (new Attributes())->setRequired()->setMultilang());

        if ($item->type === null || !class_exists($item->type)) {
            $this->addDropdown(
                '%ADMIN_TYPE%',
                'type',
                (new Attributes())->setRequired()
                    ->setOptions(
                        ElementHelper::arrayToSelectOptions(
                            ShippingHelper::getTypes(
                                $this->configuration->getVendorNameDir(),
                                $this->configuration->getAccountDir()
                            )
                        )
                    )
            );
        } elseif ($item->type !== null) {
            /** @var AbstractCollection $item */
            $item = new $item->type();
            $item->buildAdminForm($this);
        }

        $this->addSubmitButton('%CORE_SAVE%');
    }
}
