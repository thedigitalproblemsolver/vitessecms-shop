<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Blocks;

use MongoDB\BSON\ObjectID;
use VitesseCms\Block\AbstractBlockModel;
use VitesseCms\Block\Models\Block;
use VitesseCms\Content\Models\Item;
use VitesseCms\Shop\Helpers\CheckoutHelper;
use VitesseCms\Shop\Models\Shopper;

class ShopCheckoutSummary extends AbstractBlockModel
{
    public function initialize()
    {
        parent::initialize();

        $this->excludeFromCache = true;
    }

    public function parse(Block $block): void
    {
        parent::parse($block);

        $logMessage = 'Checkout summary';
        if ($this->di->get('user')->isLoggedIn()):
            Shopper::setFindValue('userId', (string)$this->di->get('user')->getId());
            $shopper = Shopper::findFirst();
            if (!$shopper) :
                $this->di->get('flash')->error('We are missing some addres information. Please fil in this form');
                $this->di->get('response')->redirect('shop/shopper/edit/');
                $logMessage .= ' but shopper is missing';
                $this->di->get('view')->disable();
            endif;

            $shiptoAddress = CheckoutHelper::getShiptoAddress($this->di->get('user'));
            $shiptoAddress->set('companyName', $shopper->_('companyName'));

            $previousStep = $this->di->get('shop')->checkout->getPreviousStep();
            $cart = $this->di->get('shop')->cart->getCartFromSession();
            $this->di->get('shop')->cart->setBlockBasics($block, $cart);
            $block->set('shopper', $shopper);
            $block->set('user', $shopper->_('user'));
            $block->set('checkoutLink', 'shop/order/saveAndPay');
            $block->set('shiptoAddresses', $shiptoAddress);
            $block->set('backLink', $previousStep->_('slug'));
        else :
            $logMessage .= ' user logged out';
            $this->di->get('flash')->setError('USER_NO_ACCESS');
            $this->di->get('response')->setStatusCode(401, 'Unauthorized')->redirect('');
        endif;

        $this->di->get('log')->write(
            new ObjectID($this->view->getCurrentId()),
            Item::class,
            $logMessage
        );
    }
}
