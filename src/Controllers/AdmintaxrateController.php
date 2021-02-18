<?php declare(strict_types=1);

namespace VitesseCms\Shop\Controllers;

use VitesseCms\Admin\AbstractAdminController;
use VitesseCms\Shop\Forms\TaxRateForm;
use VitesseCms\Shop\Models\TaxRate;

class AdmintaxrateController extends AbstractAdminController
{
    public function onConstruct()
    {
        parent::onConstruct();

        $this->class = TaxRate::class;
        $this->classForm = TaxRateForm::class;
    }
}
