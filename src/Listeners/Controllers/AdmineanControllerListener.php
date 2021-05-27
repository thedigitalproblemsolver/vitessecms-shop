<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Controllers;

use Phalcon\Events\Event;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\Shop\Controllers\AdmineanController;

class AdmineanControllerListener
{
    public function adminListFilter(Event $event, AdmineanController $controller, AdminlistFormInterface $form): string
    {
        $form->addText('%CORE_NAME%', 'filter[name]');
        $form->addPublishedField($form);

        return $form->renderForm(
            $controller->getLink() . '/' . $controller->router->getActionName(),
            'adminFilter'
        );
    }
}
