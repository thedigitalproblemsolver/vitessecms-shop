<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Controllers;

use Phalcon\Events\Event;
use Phalcon\Tag;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\Core\Factories\ObjectFactory;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;
use VitesseCms\Shop\Controllers\AdminorderController;
use VitesseCms\Shop\Enum\OrderStateEnum;
use VitesseCms\Shop\Forms\OrderOrderStateForm;
use VitesseCms\Shop\Forms\ShippingBarcodeForm;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Repositories\ShippingTypeRepository;
use VitesseCms\User\Utils\PermissionUtils;

final class AdminorderControllerListener
{
    public function __construct(private readonly ShippingTypeRepository $shippingTypeRepository)
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

        if (
            $adminorderController->user->getPermissionRole() === 'superadmin'
            && empty($order->getShippingType()->getBarcode())
        ) {
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
    }

    //TODO nog te implementeren
    public function adminListItem(Event $event, AdminorderController $controller, Order $order): void
    {
        $return = '';
        if (PermissionUtils::check(
            $controller->user,
            $controller->router->getModulePrefix() . 'shop',
            'adminorder',
            'sendemail'
        )) :
            $return .= Tag::linkTo([
                'action' => '/Admin/shop/adminorder/sendemail/' . $order->getId(),
                'class' => 'fa fa-envelope',
            ]);
        endif;

        if (PermissionUtils::check(
            $controller->user,
            $controller->router->getModulePrefix() . 'shop',
            'adminorder',
            'print'
        )) :
            $return .= Tag::linkTo(['action' => '', 'class' => 'fa fa-print']);
        endif;

        $order->setExtraAdminListButtons($return);

        if (PermissionUtils::check(
            $controller->user,
            $controller->router->getModulePrefix() . 'shop',
            'adminorder',
            'changeOrderState'
        )) :
            $tmpOrderState = ObjectFactory::create();
            if (is_string($order->_('orderState'))) :
                $tmpOrderState->set('orderStateId', $order->_('orderState'));
            else :
                $tmpOrderState->set('orderStateId', (string)$order->_('orderState')['_id']);
            endif;

            $orderStateForm = new OrderOrderStateForm($tmpOrderState);

            $form = $orderStateForm->renderForm(
                'shop/adminorder/changeOrderState/' . $order->getId(),
                'changeOrderState'
            );
            $orderStateForm = $form;
        else :
            $orderStateForm = $order->_('orderState')['name'][$controller->configuration->getLanguageShort()];
        endif;

        $adminListExtra = $controller->eventsManager->fire(
            ViewEnum::RENDER_TEMPLATE_EVENT,
            new RenderTemplateDTO(
                'orderAdminListItem',
                $controller->router->getModuleName() . '/src/Resources/views/admin/list/',
                [
                    'order' => $order,
                    'orderStateForm' => $orderStateForm,
                ]
            )
        );

        $order->setAdminListExtra($adminListExtra);
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
