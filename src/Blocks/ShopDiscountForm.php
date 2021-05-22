<?php declare(strict_types=1);

namespace VitesseCms\Shop\Blocks;

use VitesseCms\Block\AbstractBlockModel;
use VitesseCms\Block\Models\Block;
use VitesseCms\Sef\Helpers\SefHelper;
use VitesseCms\Shop\Forms\BlockShopDiscountFormForm;

class ShopDiscountForm extends AbstractBlockModel
{
    public function initialize()
    {
        parent::initialize();

        $this->excludeFromCache = true;
    }

    public function parse(Block $block): void
    {
        parent::parse($block);

        $discount = $this->di->shop->discount->loadFromSession();
        if ($discount) :
            $block->set(
                'discountUsedText',
                $this->di->language->get('SHOP_DISCOUNT_CODE_BEING_USED', [$discount->_('code')])
            );
        else :
            $form = new BlockShopDiscountFormForm($block);
            $block->set(
                'form',
                $form->renderForm(
                    SefHelper::getComponentURL('shop', 'discount', 'parsecode')
                ));
        endif;
    }
}
