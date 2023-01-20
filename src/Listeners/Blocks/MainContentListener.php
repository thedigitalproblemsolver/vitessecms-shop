<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Blocks;

use Phalcon\Events\Event;
use Phalcon\Http\Request;
use VitesseCms\Block\Models\Block;
use VitesseCms\Content\Blocks\MainContent;
use VitesseCms\Content\Models\Item;
use VitesseCms\Core\Factories\PaginationFactory;
use VitesseCms\Core\Helpers\ItemHelper;
use VitesseCms\Core\Services\UrlService;

class MainContentListener
{
    public function parse(Event $event, MainContent $mainContent, Block $block): void
    {
        $this->handleOverviewTemplates($mainContent, $block);
    }

    protected function handleOverviewTemplates(MainContent $mainContent, Block $block): void
    {
        if (substr_count($mainContent->getTemplate(), 'overview')) :
            Item::setFindValue('parentId', (string)$mainContent->getDi()->get('view')->getCurrentItem()->getId());
            Item::addFindOrder('name', 1);
            Item::setFindLimit(9999);
            $pagination = PaginationFactory::createFromArray(
                Item::findAll(),
                new Request(),
                new UrlService(new Request()),
                'page',
                $block->getInt('overviewItemLimit')
            );

            $designMapper = [];
            foreach ($pagination->getItems() as $key => $item) :
                if (isset($item->outOfStock) && $item->_('outOfStock')) :
                    unset($pagination->getItems()[$key]);
                else :
                    ItemHelper::parseBeforeMainContent($item);
                    $pagination->getItems()[$key] = $item;
                endif;

                if (
                    substr_count($mainContent->getTemplate(), 'shop_clothing_design_overview')
                    && !empty($item->_('design'))
                ) :
                    if (!isset($designMapper[$item->_('design')])) :
                        if (isset($items[$key])) :
                            $designMapper[$item->_('design')] = $key;
                            $items[$key]->set('designItems', []);
                            $items[$designMapper[$item->_('design')]]->designItems[] = $item;
                        endif;
                    else :
                        $pagination->getItems()[$designMapper[$item->_('design')]]->designItems[] = $item;
                        unset($pagination->getItems()[$key]);
                    endif;
                endif;
            endforeach;

            if (substr_count($mainContent->getTemplate(), 'shop_clothing_design_overview')) :
                foreach ($designMapper as $designId => $itemKey) :
                    if (
                        isset($pagination->getItems()[$itemKey]->designItems)
                        && count($pagination->getItems()[$itemKey]->designItems) === 1
                    ) :
                        unset($pagination->getItems()[$itemKey]->designItems);
                    else :
                        $pagination->getItems()[$itemKey]->hasDesignItems = true;
                    endif;
                endforeach;
            endif;
            $block->set('items', array_values($pagination->getItems()));

            if ($pagination->getTotalPages() > 1) :
                $block->set('pagination', $pagination);
            endif;
        endif;
    }
}
