<?php declare(strict_types=1);

namespace VitesseCms\Shop\Controllers;

use VitesseCms\Admin\AbstractAdminController;
use VitesseCms\Shop\Forms\CountryForm;
use VitesseCms\Shop\Models\Country;

class AdmincountryController extends AbstractAdminController
{
    public function onConstruct()
    {
        parent::onConstruct();

        $this->class = Country::class;
        $this->classForm = CountryForm::class;
    }
}
