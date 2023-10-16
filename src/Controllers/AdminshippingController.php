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
use VitesseCms\Shop\Enum\OrderEnum;
use VitesseCms\Shop\Enum\ShippingEnum;
use VitesseCms\Shop\Forms\ShippingForm;
use VitesseCms\Shop\Models\Shipping;
use VitesseCms\Shop\Repositories\OrderRepository;
use VitesseCms\Shop\Repositories\ShippingRepository;

final class AdminshippingController extends AbstractControllerAdmin implements
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

    private readonly OrderRepository $orderRepository;
    private readonly ShippingRepository $shippingRepository;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->orderRepository = $this->eventsManager->fire(OrderEnum::GET_REPOSITORY->value, new stdClass());
        $this->shippingRepository = $this->eventsManager->fire(ShippingEnum::GET_REPOSITORY->value, new stdClass());
    }

    public function shippingLabelAction(string $id): void
    {
        if ($id) :
            $order = $this->orderRepository->getById($id, false);
            $shippingType = $this->shippingRepository->getById((string)$order->shippingType['_id']);
            $shippingType->getLabel($order, $this->request->get('packageType'));
        endif;

        $this->viewService->disable();
    }

    public function setBarcodeAction(string $orderId): void
    {
        $order = $this->orderRepository->getById($orderId);
        $shippingType = $order->shippingType;
        $shippingType['barcode'] = trim($this->request->get('barcode'));
        $order->set('shippingType', $shippingType);
        $order->save();

        $this->redirect($this->request->getHTTPReferer());
    }

    public function getModelList(?FindValueIterator $findValueIterator): ArrayIterator
    {
        return $this->shippingRepository->findAll($findValueIterator, false);
    }

    public function getModel(string $id): ?AbstractCollection
    {
        return match ($id) {
            'new' => new Shipping(),
            default => $this->shippingRepository->getById($id, false)
        };
    }

    public function getModelForm(): AdminModelFormInterface
    {
        return new ShippingForm();
    }
}
