<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Controllers;

use ArrayIterator;
use stdClass;
use VitesseCms\Admin\Interfaces\AdminModelAddableInterface;
use VitesseCms\Admin\Interfaces\AdminModelDeletableInterface;
use VitesseCms\Admin\Interfaces\AdminModelEditableInterface;
use VitesseCms\Admin\Interfaces\AdminModelFormInterface;
use VitesseCms\Admin\Interfaces\AdminModelListInterface;
use VitesseCms\Admin\Interfaces\AdminModelPublishableInterface;
use VitesseCms\Admin\Traits\TraitAdminModelAddable;
use VitesseCms\Admin\Traits\TraitAdminModelDeletable;
use VitesseCms\Admin\Traits\TraitAdminModelEditable;
use VitesseCms\Admin\Traits\TraitAdminModelList;
use VitesseCms\Admin\Traits\TraitAdminModelPublishable;
use VitesseCms\Core\AbstractControllerAdmin;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Shop\Enum\PaymentEnum;
use VitesseCms\Shop\Forms\PaymentForm;
use VitesseCms\Shop\Models\Payment;
use VitesseCms\Shop\Repositories\PaymentRepository;

class AdminpaymentController extends AbstractControllerAdmin implements
    AdminModelAddableInterface,
    AdminModelPublishableInterface,
    AdminModelListInterface,
    AdminModelEditableInterface,
    AdminModelDeletableInterface
{
    use TraitAdminModelAddable;
    use TraitAdminModelPublishable;
    use TraitAdminModelList;
    use TraitAdminModelEditable;
    use TraitAdminModelDeletable;

    private readonly PaymentRepository $paymentRepository;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->paymentRepository = $this->eventsManager->fire(PaymentEnum::GET_REPOSITORY, new stdClass());
    }

    public function getModelList(?FindValueIterator $findValueIterator): ArrayIterator
    {
        return $this->paymentRepository->findAll($findValueIterator, false);
    }

    public function getModel(string $id): ?AbstractCollection
    {
        return match ($id) {
            'new' => new Payment(),
            default => $this->paymentRepository->getById($id, false)
        };
    }

    public function getModelForm(): AdminModelFormInterface
    {
        return new PaymentForm();
    }
}
