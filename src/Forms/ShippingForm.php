<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Forms;

use VitesseCms\Admin\Interfaces\AdminModelFormInterface;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\Helpers\ShippingHelper;
use VitesseCms\Shop\Models\Shipping;

final class ShippingForm extends AbstractForm implements AdminModelFormInterface
{
    public function buildForm(): void
    {
        if ($this->entity === null) {
            $this->entity = new Shipping();
        }

        $this->addText('%CORE_NAME%', 'name', (new Attributes())->setRequired()->setMultilang());

        if ($this->entity->type === null || !class_exists($this->entity->type)) {
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
        } elseif ($this->entity->type !== null) {
            /** @var AbstractCollection $item */
            $item = new $this->entity->type();
            $item->buildAdminForm($this);
        }

        $this->addSubmitButton('%CORE_SAVE%');
    }
}
