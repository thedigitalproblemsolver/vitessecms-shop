<?php declare(strict_types=1);

namespace VitesseCms\Shop\Forms;

use Phalcon\Mvc\View\Engine\Twig\TokenParsers\Assets;
use VitesseCms\Communication\Models\Newsletter;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Media\Enums\AssetsEnum;
use VitesseCms\Shop\Enum\OrderStateEnum;
use VitesseCms\Shop\Models\OrderState;

class OrderStateForm extends AbstractForm
{
    public function initialize( OrderState $item = null)
    {
        $this->addText('%CORE_NAME%', 'name',(new Attributes())->setMultilang())
            ->addEditor('%ADMIN_ORDERSTATE_PAGE_TEXT%', 'bodytext',(new Attributes())->setMultilang())
            ->addText('%ADMIN_SYSTEM_MESSAGE%', 'messageText',( new Attributes())->setMultilang())
            ->addDropdown(
            '%ADMIN_SYSTEM_MESSAGE_TYPE%',
            'messageType',
                (new Attributes())->setOptions(ElementHelper::arrayToSelectOptions([
                    'success' => '%ADMIN_ALERT_SUCCESS%',
                    'error' => '%ADMIN_ALERT_DANGER%',
                    'notice' => '%ADMIN_ALERT_INFO%',
                    'warning' => '%ADMIN_ALERT_WARNING%',
                ])
                ))
            ->addDropdown(
            '%ADMIN_ORDERSTATE_CHANGE_STOCK%',
            'stockAction',
                (new Attributes())->setOptions(
                    ElementHelper::arrayToSelectOptions([
                        OrderStateEnum::STOCK_ACTION_INCREASE => '%ADMIN_INCREASE%',
                        OrderStateEnum::STOCK_ACTION_DECREASE => '%ADMIN_DECREASE%',
                    ])
                ))
            ->addDropdown(
            '%ADMIN_ORDERSTATE_CHANGE_ANALYTICS_TRIGGERS%',
            'analyticsTriggers',
                (new Attributes())->setMultiple()
                    ->setOptions(ElementHelper::arrayToSelectOptions(OrderStateEnum::ANALYTICS_TRIGGERS))
            )
        ;

        Newsletter::setFindValue('parentId',null);
        $newsletters = Newsletter::findAll();
        $this->addDropdown(
            'Add to newsletter',
            'addToNewsletters',
            (new Attributes())->setMultilang()
                ->setMultiple()
                ->setInputClass(AssetsEnum::SELECT2)
                ->setOptions(ElementHelper::arrayToSelectOptions($newsletters)))
            ->addDropdown(
            'Unsubscribe from newsletter<br /><small>never end again</small>',
            'unsubscribeFromNewsletters',
                (new Attributes())->setMultilang()
                    ->setMultiple()
                    ->setInputClass(AssetsEnum::SELECT2)
                    ->setOptions(ElementHelper::arrayToSelectOptions($newsletters)))
            ->addDropdown(
            'Remove from newsletters<br /><small>Can be send again</small>',
            'removeFromNewsletters',
                (new Attributes())->setMultilang()
                    ->setMultiple()
                    ->setInputClass(AssetsEnum::SELECT2)
                    ->setOptions(ElementHelper::arrayToSelectOptions($newsletters)))
            ->addToggle('%ADMIN_ORDERSTATE_CLEAR_THE_CART%', 'clearCart')
            ->addToggle('%ADMIN_ORDERSTATE_PRINT_SHIPPING_LABEL%', 'printShippingLabel')
            ->addToggle('Can switch to state on same level', 'canSwitchToSameLevel')
            ->addText('%ADMIN_CALLING_NAME%', 'calling_name')
            ->addSubmitButton('%CORE_SAVE%')
        ;
    }
}
