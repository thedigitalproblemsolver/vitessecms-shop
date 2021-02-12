<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners;

use Phalcon\Events\Event;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Shop\Controllers\AdmindiscountController;
use VitesseCms\Shop\Controllers\AdminorderController;
use VitesseCms\Shop\Controllers\AdminorderstateController;
use VitesseCms\Shop\Enum\OrderStateEnum;
use VitesseCms\Shop\Models\OrderState;

class AdmindiscountControllerListener
{
    public function adminListFilter(
        Event $event,
        AdmindiscountController $controller,
        AdminlistFormInterface $form
    ): string
    {
        $form->addNameField($form);
        $form->addPublishedField($form);

        return $form->renderForm(
            $controller->getLink() . '/' . $controller->router->getActionName(),
            'adminFilter'
        );
    }
}
