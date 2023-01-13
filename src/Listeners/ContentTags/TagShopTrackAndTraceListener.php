<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\ContentTags;

use VitesseCms\Content\DTO\TagListenerDTO;
use VitesseCms\Content\Helpers\EventVehicleHelper;
use VitesseCms\Content\Listeners\ContentTags\AbstractTagListener;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Models\Shipping;

class TagShopTrackAndTraceListener extends AbstractTagListener
{
    public function __construct()
    {
        $this->name = 'TRACKANDTRACE';
    }

    protected function parse(EventVehicleHelper $eventVehicle, TagListenerDTO $tagListenerDTO): void
    {
        if (!empty($eventVehicle->_('orderId'))) :
            $order = Order::findById($eventVehicle->_('orderId'));
            $shipping = Shipping::findById((string)$order->_('shippingType')['_id']);

            $link = $shipping->getTrackAndTraceLink($order);
            $replace = '';
            if (!empty($link)) :
                $replace = ['<a href="' . $link . '" class="link-trackandtrace" style="text-decoration:none" target="_blank" >', '</a>'];
            endif;
            $content = str_replace(['{TRACKANDTRACE}', '{/TRACKANDTRACE}'], $replace, $eventVehicle->_('content'));
            $eventVehicle->set('content', $content);
        endif;
    }
}
