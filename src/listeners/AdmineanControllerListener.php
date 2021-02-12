<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners;

use Phalcon\Events\Event;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Shop\Controllers\AdmincountryController;
use VitesseCms\Shop\Controllers\AdmineanController;
use VitesseCms\Shop\Controllers\AdminorderController;
use VitesseCms\Shop\Controllers\AdminorderstateController;
use VitesseCms\Shop\Enum\OrderStateEnum;
use VitesseCms\Shop\Models\OrderState;

class AdmineanControllerListener
{
    public function adminListFilter(
        Event $event,
        AdmineanController $controller,
        AdminlistFormInterface $form
    ): string
    {
        $form->addText('%CORE_NAME%', 'filter[name]');
        $form->addPublishedField($form);

        return $form->renderForm(
            $controller->getLink() . '/' . $controller->router->getActionName(),
            'adminFilter'
        );
    }
}
