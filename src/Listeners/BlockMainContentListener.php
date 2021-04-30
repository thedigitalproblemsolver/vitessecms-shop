<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners;

use Phalcon\Events\Event;
use VitesseCms\Block\Models\Block;
use VitesseCms\Block\Models\BlockMainContent;
use VitesseCms\Content\Models\Item;
use VitesseCms\Core\Helpers\ItemHelper;
use VitesseCms\Datagroup\Models\Datagroup;

class BlockMainContentListener
{
    public function parse(Event $event, BlockMainContent $blockMainContent, Block $block): void
    {
        $this->handleOverviewTemplates($blockMainContent, $block);
    }

    protected function handleOverviewTemplates(BlockMainContent $blockMainContent, Block $block): void
    {
        if (substr_count($blockMainContent->getTemplate(), 'overview')) :
            Item::setFindValue('parentId', (string)$blockMainContent->getDi()->view->getCurrentItem()->getId());
            Item::addFindOrder('name', 1);
            Item::setFindLimit(9999);
            $items = Item::findAll();
            $designMapper = [];
            foreach ($items as $key => $item) :
                if (isset($item->outOfStock) && $item->_('outOfStock')) :
                    unset($items[$key]);
                else :
                    ItemHelper::parseBeforeMainContent($item);
                    $items[$key] = $item;
                endif;

                if (
                    substr_count($blockMainContent->getTemplate(), 'shop_clothing_design_overview')
                    && !empty($item->_('design'))
                ) :
                    if (!isset($designMapper[$item->_('design')])) :
                        if (isset($items[$key])) :
                            $designMapper[$item->_('design')] = $key;
                            $items[$key]->set('designItems', []);
                            $items[$designMapper[$item->_('design')]]->designItems[] = $item;
                        endif;
                    else :
                        $items[$designMapper[$item->_('design')]]->designItems[] = $item;
                        unset($items[$key]);
                    endif;
                endif;
            endforeach;

            if (substr_count($blockMainContent->getTemplate(), 'shop_clothing_design_overview')) :
                foreach ($designMapper as $designId => $itemKey) :
                    if (
                        isset($items[$itemKey]->designItems)
                        && count($items[$itemKey]->designItems) === 1
                    ) :
                        unset($items[$itemKey]->designItems);
                    else :
                        $items[$itemKey]->hasDesignItems = true;
                    endif;
                endforeach;
            endif;
            $block->set('items', array_values($items));
        endif;
    }
}
