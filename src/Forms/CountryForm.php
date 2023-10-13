<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Forms;

use VitesseCms\Admin\Interfaces\AdminModelFormInterface;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;

final class CountryForm extends AbstractForm implements AdminModelFormInterface
{
    public function BuildForm(): void
    {
        $this->addText(
            '%CORE_NAME%',
            'name',
            (new Attributes())->setRequired()->setMultilang()
        )
            ->addText('%ADMIN_COUNTRY_TWO_CODE%', 'short', (new Attributes())->setRequired())
            ->addText('%ADMIN_COUNTRY_THREE_CODE%', 'shortThree', (new Attributes())->setRequired())
            ->addSubmitButton('%CORE_SAVE%');
    }
}
