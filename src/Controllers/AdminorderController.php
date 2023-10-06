<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Controllers;

use ArrayIterator;
use stdClass;
use VitesseCms\Admin\Interfaces\AdminModelEditableInterface;
use VitesseCms\Admin\Interfaces\AdminModelFormInterface;
use VitesseCms\Admin\Interfaces\AdminModelListInterface;
use VitesseCms\Admin\Traits\TraitAdminModelEditable;
use VitesseCms\Admin\Traits\TraitAdminModelList;
use VitesseCms\Core\AbstractControllerAdmin;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Shop\Enum\OrderEnum;
use VitesseCms\Shop\Forms\OrderForm;
use VitesseCms\Shop\Helpers\OrderHelper;
use VitesseCms\Shop\Repositories\OrderRepository;

final class AdminorderController extends AbstractControllerAdmin implements
    AdminModelListInterface,
    AdminModelEditableInterface
{
    use TraitAdminModelList;
    use TraitAdminModelEditable;

    private readonly OrderRepository $orderRepository;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->orderRepository = $this->eventsManager->fire(OrderEnum::GET_REPOSITORY->value, new stdClass());
    }

    public function changeOrderStateAction(): void
    {
        if ($this->dispatcher->getParam(0) !== null) :
            $order = $this->repositories->order->getById($this->dispatcher->getParam(0), false);
            if ($order !== null) :
                OrderHelper::setOrderState(
                    $order,
                    $this->repositories->orderState->getById($this->request->get('orderState'))
                );
                $order->save();

                $this->flash->setSucces('ADMIN_STATE_CHANGE_SUCCESS', ['Order']);
            endif;
        endif;

        $this->redirect();
    }

    public function sendEmailAction(): void
    {
        if ($this->dispatcher->getParam(0) !== null) :
            OrderHelper::sendEmail(
                $this->repositories->order->getById($this->dispatcher->getParam(0)),
                $this->view
            );
        endif;

        $this->redirect();
    }

    public function getModelList(?FindValueIterator $findValueIterator): ArrayIterator
    {
        return $this->orderRepository->findAll(
            $findValueIterator,
            false
        );
    }

    public function getModel(string $id): ?AbstractCollection
    {
        return $this->orderRepository->getById($id);
    }

    public function getModelForm(): AdminModelFormInterface
    {
        return new OrderForm();
    }

    protected function getTemplate(): string
    {
        return 'adminOrderForm';
    }
}
