<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Listeners;

use VitesseCms\Content\Blocks\MainContent;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Shop\Enum\CountryEnum;
use VitesseCms\Shop\Enum\OrderEnum;
use VitesseCms\Shop\Enum\PaymentEnum;
use VitesseCms\Shop\Enum\ShopperEnum;
use VitesseCms\Shop\Enum\TaxRateEnum;
use VitesseCms\Shop\Fields\ShopPrice;
use VitesseCms\Shop\Listeners\Admin\AdminMenuListener;
use VitesseCms\Shop\Listeners\Blocks\MainContentListener;
use VitesseCms\Shop\Listeners\ContentTags\TagDiscountListener;
use VitesseCms\Shop\Listeners\ContentTags\TagOrderSendDateListener;
use VitesseCms\Shop\Listeners\ContentTags\TagShopTrackAndTraceListener;
use VitesseCms\Shop\Listeners\Fields\PriceListener;
use VitesseCms\Shop\Listeners\Models\CountryListener;
use VitesseCms\Shop\Listeners\Models\OrderListener;
use VitesseCms\Shop\Listeners\Models\PaymentListener;
use VitesseCms\Shop\Listeners\Models\ShopperListener;
use VitesseCms\Shop\Listeners\Models\TaxRateListener;
use VitesseCms\Shop\Models\Country;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Models\Payment;
use VitesseCms\Shop\Models\Shopper;
use VitesseCms\Shop\Models\TaxRate;

final class InitiateListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $injectable): void
    {
        if ($injectable->configuration->isEcommerce()):
            if ($injectable->user->hasAdminAccess()):
                $injectable->eventsManager->attach('adminMenu', new AdminMenuListener());
                $injectable->eventsManager->attach(ShopPrice::class, new PriceListener());
            endif;
            $injectable->eventsManager->attach(MainContent::class, new MainContentListener());
            $injectable->eventsManager->attach('discount', new DiscountListener($injectable->shop));
            $injectable->eventsManager->attach('user', new DiscountListener($injectable->shop));
            $injectable->eventsManager->attach('contentTag', new TagDiscountListener());
            $injectable->eventsManager->attach('contentTag', new TagOrderSendDateListener());
            $injectable->eventsManager->attach('contentTag', new TagShopTrackAndTraceListener());
            $injectable->eventsManager->attach(ShopPrice::class, new PriceListener());
            self::addModels($injectable);
        endif;
    }

    private static function addModels(InjectableInterface $di): void
    {
        $di->eventsManager->attach(CountryEnum::LISTENER->value, new CountryListener(Country::class));
        $di->eventsManager->attach(OrderEnum::LISTENER->value, new OrderListener(Order::class));
        $di->eventsManager->attach(TaxRateEnum::LISTENER->value, new TaxRateListener(TaxRate::class));
        $di->eventsManager->attach(CountryEnum::LISTENER->value, new CountryListener(Country::class));
        $di->eventsManager->attach(ShopperEnum::LISTENER->value, new ShopperListener(Shopper::class));
        $di->eventsManager->attach(PaymentEnum::LISTENER, new PaymentListener(Payment::class));
    }
}
