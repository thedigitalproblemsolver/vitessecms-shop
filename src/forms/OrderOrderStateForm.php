<?php declare(strict_types=1);

namespace VitesseCms\Shop\Forms;

use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\Models\OrderState;

class OrderOrderStateForm extends AbstractForm
{
    public function initialize( BaseObjectInterface $item = null)
    {
        //TODO waar gaat het mis? De reset hoort hier niet
        OrderState::reset();
        $currentOrderState = OrderState::findById($item->_('orderStateId'));
        OrderState::reset();
        OrderState::setFindValue('parentId',$item->_('orderStateId'));
        $orderStates = OrderState::findAll();
        if(count($orderStates) > 0 ) :
            $this->addDropdown(
                $currentOrderState->_('name'),
                'orderState',
                (new Attributes())->setOptions(ElementHelper::arrayToSelectOptions(
                    $orderStates, [$item->_('orderStateId')]
                ))
            );
        elseif(\is_object($currentOrderState) && (bool)$currentOrderState->_('canSwitchToSameLevel')) :
            OrderState::setFindValue('parentId',$currentOrderState->_('parentId'));
            $orderStates = OrderState::findAll();
            $this->addDropdown(
                $currentOrderState->_('name'),
                'orderState',
                (new Attributes())->setOptions(ElementHelper::arrayToSelectOptions(
                        $orderStates,
                        [$item->_('orderStateId')]
                    )
                )
            );
        else :
            $this->addHtml($currentOrderState?$currentOrderState->_('name'):'');
        endif;
    }
}
