<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners;

use Phalcon\Events\Manager;
use VitesseCms\Content\Blocks\MainContent;

class InitiateListeners
{
    public static function setListeners(Manager $eventsManager): void
    {
        $eventsManager->attach('adminMenu', new AdminMenuListener());
        $eventsManager->attach(MainContent::class, new BlockMainContentListener());
    }
}
