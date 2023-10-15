<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Controllers;

use Phalcon\Events\Event;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\Controllers\AdminorderController;
use VitesseCms\Shop\Enum\OrderStateEnum;
use VitesseCms\Shop\Forms\OrderOrderStateForm;
use VitesseCms\Shop\Forms\ShippingBarcodeForm;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Repositories\ShippingRepository;

final class AdminorderControllerListener
{
    public function __construct(private readonly ShippingRepository $shippingTypeRepository)
    {
    }

    public function beforeEditModel(Event $event, AdminorderController $adminorderController, Order $order): void
    {
        $shippingType = $this->shippingTypeRepository->getById((string)$order->getShippingType()->getId());
        if ($shippingType) {
            $adminorderController->addFormParams('shippingLabelLink', $shippingType->getLabelLink($order));
        }

        if (!empty($order->getAffiliateId())) {
            $adminorderController->addFormParams(
                'affiliateName',
                $adminorderController->repositories->item->getById($order->getAffiliateId(), false)
            );
        }

        if (empty($order->getShippingType()->getBarcode())) {
            $barcodeForm = new ShippingBarcodeForm();
            $barcodeForm->addText('Barcode', 'barcode');
            $adminorderController->addFormParams(
                'barcodeForm',
                $barcodeForm->renderForm(
                    'admin/shop/adminshipping/setBarcode/' . $order->getId(),
                    'barcodeForm',
                    true
                )
            );
        }

        $adminorderController->addFormParams(
            'orderStateForm',
            $this->getOrderStateForm((string)$order->orderState['_id'], (string)$order->getId())
        );
    }

    private function getOrderStateForm(string $orderStateId, string $orderId): string
    {
        $orderStateForm = new OrderOrderStateForm();
        $orderStateForm->buildForm($orderStateId);

        return $orderStateForm->renderForm(
            'admin/shop/adminorder/changeOrderState/' . $orderId,
            'changeOrderState'
        );
    }

    public function adminListFilter(
        Event $event,
        AdminorderController $controller,
        AdminlistFormInterface $form
    ): void {
        $form->addNumber('%SHOP_ORDERID%', 'filter[orderId]')
            ->addDropdown(
                'Order state',
                'filter[orderState.calling_name]',
                (new Attributes())->setOptions(
                    ElementHelper::arrayToSelectOptions(OrderStateEnum::ORDER_STATES)
                )
            );

        if ($form->setting !== null && $form->setting->has('SHOP_DATAGROUP_AFFILIATE')) :
            $form->addDropdown(
                'Affiliate property',
                'filter[affiliateId]',
                (new Attributes())->setOptions(
                    ElementHelper::modelIteratorToOptions(
                        $controller->repositories->item->findAll(
                            new FindValueIterator(
                                [new FindValue('datagroup', $form->setting->get('SHOP_DATAGROUP_AFFILIATE'))]
                            )
                        )
                    )
                )
            );
        endif;
    }
}
