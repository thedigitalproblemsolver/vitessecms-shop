<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Blocks;

use DateTime;
use MongoDB\BSON\ObjectId;
use Phalcon\Events\Event;
use VitesseCms\Block\Forms\BlockForm;
use VitesseCms\Block\Models\Block;
use VitesseCms\Content\Models\Item;
use VitesseCms\Core\Utils\CookieUtil;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\Blocks\AffiliateInitialize;

class AffiliateInitializeListener
{
    public function buildBlockForm(Event $event, BlockForm $form): void
    {
        $form->addDropdown(
            'Entry point datagroups',
            'datagroups',
            (new Attributes())->setMultiple(true)
                ->setInputClass('select2')
                ->setOptions(
                    ElementHelper::modelIteratorToOptions($form->di->get('repositories')->datagroup->findAll()))
        )
        ->addNumber('Cookie lifetime in days', 'cookieLifetime');
    }

    public function loadAssets(Event $event, AffiliateInitialize $affiliateInitialize, Block $block): void
    {
        $itemId = $block->getDI()->get('view')->getCurrentId();
        $referer = $block->getDI()->get('request')->getServer('HTTP_REFERER');
        $websiteUrl = parse_url($block->getDI()->get('view')->getCurrentItem()->_('website'));
        $website = '';
        if (isset($websiteUrl['host'])) :
            $website = str_replace('www.', '', $websiteUrl['host']);
        endif;

        $inQuery = false;
        if ($block->getDI()->get('request')->get('a')) :
            $itemId = $block->getDI()->get('request')->get('a');
            $inQuery = true;
        endif;

        if (
            (
                $inQuery
                && !$block->getDI()->get('session')->has('affiliateParsed')
            ) || (
                in_array(
                    $block->getDI()->get('view')->getCurrentItem()->getDatagroup(),
                    $block->_('datagroups'),
                    true
                )
                && substr_count($website, $referer) > 0
                && !$block->getDI()->get('session')->has('affiliateParsed')
            )
        ) :
            CookieUtil::set(
                'affiliate-source',
                $itemId,
                (new DateTime())->modify('+30 days')->getTimestamp()
            );
            $block->getDI()->get('log')->write(
                new ObjectId($itemId),
                Item::class,
                'Affiliate entry for ' . $website . ' from ' . $referer
            );
        endif;

        $block->getDI()->get('session')->set('affiliateParsed', true);
    }
}
