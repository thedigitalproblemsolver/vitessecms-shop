<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners;

use Phalcon\Events\Manager;
use VitesseCms\Shop\Blocks\AffiliateInitialize;
use VitesseCms\Shop\Controllers\AdmincountryController;
use VitesseCms\Shop\Controllers\AdmindiscountController;
use VitesseCms\Shop\Controllers\AdmineanController;
use VitesseCms\Shop\Controllers\AdminorderController;
use VitesseCms\Shop\Controllers\AdminorderstateController;

class InitiateAdminListeners
{
    public static function setListeners(Manager $eventsManager): void
    {
        $eventsManager->attach('adminMenu', new AdminMenuListener());
        $eventsManager->attach(AdminorderController::class, new AdminorderControllerListener());
        $eventsManager->attach(AdminorderstateController::class, new AdminorderstateControllerListener());
        $eventsManager->attach(AdmincountryController::class, new AdmincountryControllerListener());
        $eventsManager->attach(AdmindiscountController::class, new AdmindiscountControllerListener());
        $eventsManager->attach(AdmineanController::class, new AdmineanControllerListener());
        $eventsManager->attach(AffiliateInitialize::class, new BlockAffiliateInitializeListener());
    }
}
