<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners;

use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Shop\Blocks\AffiliateInitialize;
use VitesseCms\Shop\Controllers\AdmincountryController;
use VitesseCms\Shop\Controllers\AdmindiscountController;
use VitesseCms\Shop\Controllers\AdmineanController;
use VitesseCms\Shop\Controllers\AdminorderController;
use VitesseCms\Shop\Controllers\AdminorderstateController;
use VitesseCms\Shop\Fields\ShopSizeAndColor;
use VitesseCms\Shop\Listeners\Admin\AdminMenuListener;
use VitesseCms\Shop\Listeners\Blocks\AffiliateInitializeListener;
use VitesseCms\Shop\Listeners\Controllers\AdmincountryControllerListener;
use VitesseCms\Shop\Listeners\Controllers\AdmindiscountControllerListener;
use VitesseCms\Shop\Listeners\Controllers\AdmineanControllerListener;
use VitesseCms\Shop\Listeners\Controllers\AdminorderControllerListener;
use VitesseCms\Shop\Listeners\Controllers\AdminorderstateControllerListener;
use VitesseCms\Shop\Listeners\Fields\SizeAndColorListener;

class InitiateAdminListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        if($di->configuration->isEcommerce()):
            $di->eventsManager->attach('adminMenu', new AdminMenuListener());
            $di->eventsManager->attach(AdminorderController::class, new AdminorderControllerListener());
            $di->eventsManager->attach(AdminorderstateController::class, new AdminorderstateControllerListener());
            $di->eventsManager->attach(AdmincountryController::class, new AdmincountryControllerListener());
            $di->eventsManager->attach(AdmindiscountController::class, new AdmindiscountControllerListener());
            $di->eventsManager->attach(AdmineanController::class, new AdmineanControllerListener());
            $di->eventsManager->attach(AffiliateInitialize::class, new AffiliateInitializeListener());
            $di->eventsManager->attach(ShopSizeAndColor::class, new SizeAndColorListener());
        endif;
    }
}
