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
    public static function setListeners(InjectableInterface $di): void
    {
        if ($di->configuration->isEcommerce()):
            if ($di->user->hasAdminAccess()):
                $di->eventsManager->attach('adminMenu', new AdminMenuListener());
                $di->eventsManager->attach(ShopPrice::class, new PriceListener());
            endif;
            $di->eventsManager->attach(MainContent::class, new MainContentListener());
            $di->eventsManager->attach('discount', new DiscountListener($di->shop));
            $di->eventsManager->attach('user', new DiscountListener($di->shop));
            $di->eventsManager->attach('contentTag', new TagDiscountListener());
            $di->eventsManager->attach('contentTag', new TagOrderSendDateListener());
            $di->eventsManager->attach('contentTag', new TagShopTrackAndTraceListener());
            $di->eventsManager->attach(ShopPrice::class, new PriceListener());
            self::addModels($di);
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
