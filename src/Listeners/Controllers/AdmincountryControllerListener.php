<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Controllers;

use Phalcon\Events\Event;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\Shop\Controllers\AdmincountryController;

final class AdmincountryControllerListener
{
    public function adminListFilter(
        Event $event,
        AdmincountryController $controller,
        AdminlistFormInterface $form
    ): void {
        $form->addNameField($form);
        $form->addPublishedField($form);
    }
}
