<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Controllers;

use Phalcon\Events\Event;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\Shop\Controllers\AdmindiscountController;

class AdmindiscountControllerListener
{
    public function adminListFilter(Event $event, AdmindiscountController $controller, AdminlistFormInterface $form): string
    {
        $form->addNameField($form);
        $form->addPublishedField($form);

        return $form->renderForm(
            $controller->getLink() . '/' . $controller->router->getActionName(),
            'adminFilter'
        );
    }
}
