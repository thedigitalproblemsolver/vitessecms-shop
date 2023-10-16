<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Forms;

use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\Models\OrderState;

use function is_object;

class OrderOrderStateForm extends AbstractForm
{
    public function initialize()
    {
    }

    public function buildForm(string $orderStateId)
    {
        $currentOrderState = OrderState::findById($orderStateId);
        OrderState::reset();
        OrderState::setFindValue('parentId', $orderStateId);
        $orderStates = OrderState::findAll();
        if (count($orderStates) > 0) :
            $this->addDropdown(
                $currentOrderState->_('name'),
                'orderState',
                (new Attributes())->setOptions(
                    ElementHelper::arrayToSelectOptions(
                        $orderStates, [$orderStateId]
                    )
                )
            );
        elseif (is_object($currentOrderState) && (bool)$currentOrderState->_('canSwitchToSameLevel')) :
            OrderState::setFindValue('parentId', $currentOrderState->_('parentId'));
            $orderStates = OrderState::findAll();
            $this->addDropdown(
                $currentOrderState->_('name'),
                'orderState',
                (new Attributes())->setOptions(
                    ElementHelper::arrayToSelectOptions(
                        $orderStates,
                        [$orderStateId]
                    )
                )
            );
        else :
            $this->addHtml($currentOrderState ? $currentOrderState->_('name') : '');
        endif;
    }
}
