<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Listeners;

use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Shop\Blocks\AffiliateInitialize;
use VitesseCms\Shop\Controllers\AdmincountryController;
use VitesseCms\Shop\Controllers\AdmindiscountController;
use VitesseCms\Shop\Controllers\AdmineanController;
use VitesseCms\Shop\Controllers\AdminorderController;
use VitesseCms\Shop\Controllers\AdminorderstateController;
use VitesseCms\Shop\Enum\CountryEnum;
use VitesseCms\Shop\Enum\OrderEnum;
use VitesseCms\Shop\Enum\OrderStateEnum;
use VitesseCms\Shop\Enum\PaymentEnum;
use VitesseCms\Shop\Enum\ShippingEnum;
use VitesseCms\Shop\Enum\ShopperEnum;
use VitesseCms\Shop\Enum\TaxRateEnum;
use VitesseCms\Shop\Fields\ShopSizeAndColor;
use VitesseCms\Shop\Listeners\Admin\AdminMenuListener;
use VitesseCms\Shop\Listeners\Blocks\AffiliateInitializeListener;
use VitesseCms\Shop\Listeners\Controllers\AdmincountryControllerListener;
use VitesseCms\Shop\Listeners\Controllers\AdmindiscountControllerListener;
use VitesseCms\Shop\Listeners\Controllers\AdmineanControllerListener;
use VitesseCms\Shop\Listeners\Controllers\AdminorderControllerListener;
use VitesseCms\Shop\Listeners\Controllers\AdminorderstateControllerListener;
use VitesseCms\Shop\Listeners\Fields\SizeAndColorListener;
use VitesseCms\Shop\Listeners\Models\CountryListener;
use VitesseCms\Shop\Listeners\Models\OrderListener;
use VitesseCms\Shop\Listeners\Models\OrderStateListener;
use VitesseCms\Shop\Listeners\Models\PaymentListener;
use VitesseCms\Shop\Listeners\Models\ShippingListener;
use VitesseCms\Shop\Listeners\Models\ShopperListener;
use VitesseCms\Shop\Listeners\Models\TaxRateListener;
use VitesseCms\Shop\Listeners\Models\UserListener;
use VitesseCms\Shop\Models\Country;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Models\Payment;
use VitesseCms\Shop\Models\Shipping;
use VitesseCms\Shop\Models\Shopper;
use VitesseCms\Shop\Models\TaxRate;
use VitesseCms\Shop\Repositories\OrderRepository;
use VitesseCms\Shop\Repositories\OrderStateRepository;
use VitesseCms\Shop\Repositories\ShippingRepository;
use VitesseCms\Shop\Repositories\ShipToAddressRepository;
use VitesseCms\Shop\Repositories\ShopperRepository;
use VitesseCms\User\Models\User;

class InitiateAdminListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        if ($di->configuration->isEcommerce()):
            $di->eventsManager->attach('adminMenu', new AdminMenuListener());

            self::addControllers($di);
            self::addModels($di);

            $di->eventsManager->attach(AffiliateInitialize::class, new AffiliateInitializeListener());
            $di->eventsManager->attach(ShopSizeAndColor::class, new SizeAndColorListener());

            $di->eventsManager->attach(
                User::class,
                new UserListener(
                    $di->log,
                    new ShopperRepository(),
                    new ShipToAddressRepository(),
                    new OrderRepository(Order::class)
                )
            );
        endif;
    }

    private static function addControllers(InjectableInterface $di): void
    {
        $di->eventsManager->attach(
            AdminorderController::class,
            new AdminorderControllerListener(
                new ShippingRepository(Shipping::class)
            )
        );
        $di->eventsManager->attach(AdminorderstateController::class, new AdminorderstateControllerListener());
        $di->eventsManager->attach(AdmincountryController::class, new AdmincountryControllerListener());
        $di->eventsManager->attach(AdmindiscountController::class, new AdmindiscountControllerListener());
        $di->eventsManager->attach(AdmineanController::class, new AdmineanControllerListener());
    }

    private static function addModels(InjectableInterface $di): void
    {
        $di->eventsManager->attach(OrderEnum::LISTENER->value, new OrderListener(Order::class));
        $di->eventsManager->attach(ShippingEnum::LISTENER->value, new ShippingListener(Shipping::class));
        $di->eventsManager->attach(PaymentEnum::LISTENER, new PaymentListener(Payment::class));
        $di->eventsManager->attach(CountryEnum::LISTENER->value, new CountryListener(Country::class));
        $di->eventsManager->attach(OrderStateEnum::LISTENER, new OrderStateListener(new OrderStateRepository()));
        $di->eventsManager->attach(TaxRateEnum::LISTENER->value, new TaxRateListener(TaxRate::class));
        $di->eventsManager->attach(ShopperEnum::LISTENER->value, new ShopperListener(Shopper::class));
    }
}
