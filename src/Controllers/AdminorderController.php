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
use VitesseCms\Shop\Enum\OrderStateEnum;
use VitesseCms\Shop\Forms\OrderForm;
use VitesseCms\Shop\Helpers\OrderHelper;
use VitesseCms\Shop\Repositories\OrderRepository;
use VitesseCms\Shop\Repositories\OrderStateRepository;

final class AdminorderController extends AbstractControllerAdmin implements
    AdminModelListInterface,
    AdminModelEditableInterface
{
    use TraitAdminModelList;
    use TraitAdminModelEditable;

    private readonly OrderRepository $orderRepository;
    private readonly OrderStateRepository $orderStateRepository;

    public function onConstruct(): void
    {
        parent::onConstruct();

        $this->orderRepository = $this->eventsManager->fire(OrderEnum::GET_REPOSITORY->value, new stdClass());
        $this->orderStateRepository = $this->eventsManager->fire(OrderStateEnum::GET_REPOSITORY, new stdClass());
    }

    public function changeOrderStateAction(string $id): void
    {
        $order = $this->orderRepository->getById($id, false);
        if ($order !== null) :
            OrderHelper::setOrderState(
                $order,
                $this->orderStateRepository->getById($this->request->get('orderState'))
            );
            $order->save();

            $this->flashService->setSucces('ADMIN_STATE_CHANGE_SUCCESS', ['Order']);
        endif;

        $this->redirect($this->request->getHTTPReferer());
    }

    public function sendEmailAction(string $id): void
    {
        OrderHelper::sendEmail($this->orderRepository->getById($id), $this->viewService);

        $this->redirect($this->request->getHTTPReferer());
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

    protected function adminListWithPaginationTemplate(): string
    {
        return 'adminOrderListWithPagination';
    }
}
