<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Fields;

use Phalcon\Events\Event;
use VitesseCms\Database\AbstractCollection;

class PriceListener
{
    public function parse(Event $event, AbstractCollection $item): void
    {
        if( !empty($item->_('price')) && empty($item->_('price_sale'))) :
            $item->set('price_sale', $item->_('price'));
        endif;
    }
}