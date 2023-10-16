<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Blocks;

use MongoDB\BSON\ObjectID;
use stdClass;
use VitesseCms\Admin\Helpers\PaginationHelper;
use VitesseCms\Block\AbstractBlockModel;
use VitesseCms\Block\Models\Block;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;
use VitesseCms\Shop\Enum\OrderEnum;
use VitesseCms\Shop\Repositories\OrderRepository;

final class ShopUserOrders extends AbstractBlockModel
{
    private readonly OrderRepository $orderRepository;

    public function initialize()
    {
        parent::initialize();

        $this->excludeFromCache = true;
        $this->orderRepository = $this->di->get('eventsManager')->fire(
            OrderEnum::GET_REPOSITORY->value,
            new stdClass()
        );
    }

    public function parse(Block $block): void
    {
        if ($this->di->get('user')->isLoggedIn()) {
            $orders = $this->orderRepository->findAll(
                new FindValueIterator([
                    new FindValue(
                        'shopper.user._id',
                        new ObjectID((string)$this->di->get('user')->getId())
                    )
                ]),
                false
            );

            $pagination = new PaginationHelper(
                $orders,
                $this->di->get('url'),
                $this->di->get('request')->get('offset', 'int', 0)
            );

            $orderList = $this->di->get('eventsManager')->fire(
                ViewEnum::RENDER_TEMPLATE_EVENT,
                new RenderTemplateDTO(
                    'blocks/orderlist',
                    '',
                    ['pagination' => $pagination]
                )
            );
            
            $block->set('orderList', $orderList);
        }
    }
}
