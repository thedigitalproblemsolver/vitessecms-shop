<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Controllers;

use ArrayIterator;
use stdClass;
use VitesseCms\Admin\Interfaces\AdminModelAddableInterface;
use VitesseCms\Admin\Interfaces\AdminModelEditableInterface;
use VitesseCms\Admin\Interfaces\AdminModelFormInterface;
use VitesseCms\Admin\Interfaces\AdminModelListInterface;
use VitesseCms\Admin\Interfaces\AdminModelPublishableInterface;
use VitesseCms\Admin\Traits\TraitAdminModelAddable;
use VitesseCms\Admin\Traits\TraitAdminModelEditable;
use VitesseCms\Admin\Traits\TraitAdminModelList;
use VitesseCms\Admin\Traits\TraitAdminModelPublishable;
use VitesseCms\Core\AbstractControllerAdmin;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Shop\Enum\CountryEnum;
use VitesseCms\Shop\Forms\CountryForm;
use VitesseCms\Shop\Models\Country;
use VitesseCms\Shop\Repositories\CountryRepository;

final class AdmincountryController extends AbstractControllerAdmin implements
    AdminModelEditableInterface,
    AdminModelListInterface,
    AdminModelPublishableInterface,
    AdminModelAddableInterface
{
    use TraitAdminModelEditable;
    use TraitAdminModelList;
    use TraitAdminModelPublishable;
    use TraitAdminModelAddable;

    private readonly CountryRepository $countryRepository;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->countryRepository = $this->eventsManager->fire(CountryEnum::GET_REPOSITORY->value, new stdClass());
    }

    public function getModel(string $id): ?AbstractCollection
    {
        return match ($id) {
            'new' => new Country(),
            default => $this->countryRepository->getById($id, false)
        };
    }

    public function getModelForm(): AdminModelFormInterface
    {
        return new CountryForm();
    }

    public function getModelList(?FindValueIterator $findValueIterator): ArrayIterator
    {
        return $this->countryRepository->findAll(
            $findValueIterator,
            false
        );
    }
}
