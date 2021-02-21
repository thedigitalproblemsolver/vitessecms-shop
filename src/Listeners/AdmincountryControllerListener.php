<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners;

use Phalcon\Events\Event;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\Shop\Controllers\AdmincountryController;

class AdmincountryControllerListener
{
    public function adminListFilter(
        Event $event,
        AdmincountryController $controller,
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
