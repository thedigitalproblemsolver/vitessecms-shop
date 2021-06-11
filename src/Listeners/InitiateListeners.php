<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners;

use VitesseCms\Content\Blocks\MainContent;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Shop\Listeners\Admin\AdminMenuListener;
use VitesseCms\Shop\Listeners\Blocks\MainContentListener;

class InitiateListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        if($di->user->hasAdminAccess()):
            $di->eventsManager->attach('adminMenu', new AdminMenuListener());
        endif;
        $di->eventsManager->attach(MainContent::class, new MainContentListener());
        $di->eventsManager->attach('discount', new DiscountListener($di->shop));
        $di->eventsManager->attach('user', new DiscountListener($di->shop));
    }
}
