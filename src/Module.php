<?php

declare(strict_types=1);

namespace VitesseCms\Shop;

use Phalcon\Di\DiInterface;
use VitesseCms\Communication\Services\MailchimpService;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Core\AbstractModule;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Models\Payment;
use VitesseCms\Shop\Models\Shipping;
use VitesseCms\Shop\Repositories\DiscountRepository;
use VitesseCms\Shop\Repositories\OrderRepository;
use VitesseCms\Shop\Repositories\OrderStateRepository;
use VitesseCms\Shop\Repositories\PaymentRepository;
use VitesseCms\Shop\Repositories\RepositoryCollection;
use VitesseCms\Shop\Repositories\ShippingTypeRepository;
use VitesseCms\Shop\Repositories\ShopperRepository;

class Module extends AbstractModule
{
    public function registerServices(DiInterface $di, string $string = null)
    {
        parent::registerServices($di, 'Shop');

        $di->setShared(
            'mailchimp',
            new MailchimpService(
                $di->get('session'),
                $di->get('setting'),
                $di->get('url'),
                $di->get('configuration')
            )
        );

        $di->setShared(
            'repositories',
            new RepositoryCollection(
                new ShippingTypeRepository(Shipping::class),
                new ItemRepository(),
                new OrderRepository(Order::class),
                new ShopperRepository(),
                new PaymentRepository(Payment::class),
                new DiscountRepository(),
                new OrderStateRepository(),
                new DatagroupRepository(),
                new DatafieldRepository()
            )
        );
    }
}
