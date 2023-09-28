<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Blocks;

use MongoDB\BSON\ObjectID;
use Phalcon\Di\Di;
use VitesseCms\Block\AbstractBlockModel;
use VitesseCms\Block\Models\Block;
use VitesseCms\Content\Models\Item;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Form\Forms\BaseForm;
use VitesseCms\Shop\Models\Shopper;
use VitesseCms\User\Forms\LoginForm;

class ShopCheckoutInformation extends AbstractBlockModel
{
    public function initialize()
    {
        parent::initialize();

        $this->excludeFromCache = true;
        $this->di = Di::getDefault();
    }

    public function parse(Block $block): void
    {
        parent::parse($block);

        $logMessage = 'Checkout information';
        if ($this->di->get('user')->isLoggedIn()):
            Shopper::setFindValue('userId', (string)$this->di->get('user')->getId());
            $shopper = Shopper::findFirst();

            if (!$shopper) :
                $this->di->flash->error('We are missing some addres information. Please fil in this form');
                $this->di->response->redirect($this->di->url->getBaseUri() . 'shop/shopper/edit/');
                $logMessage .= ' but shopper is missing';
                $this->di->view->disable();
            else :
                $datagroup = $this->di->get('setting')->get('SHOP_DATAGROUP_SHOPPERSHIPTO');
                Item::setFindValue('userId', (string)$this->di->get('user')->getId());
                Item::setFindValue('datagroup', $datagroup);
                $shiptoAddresses = Item::findAll();

                Item::setFindValue('datagroup', $this->di->get('setting')->get('SHOP_DATAGROUP_CHECKOUT'));
                /** @var AbstractCollection $checkoutPage */
                $checkoutPage = Item::findFirst();
                /** @var AbstractCollection $nextStep */
                $nextStep = $this->di->get('shop')->checkout->getNextStep();

                $block->set('checkoutLink', $nextStep->_('slug'));
                $block->set('cartLink', $checkoutPage->_('slug'));
                $block->set('shopper', $shopper);
                //TODO set to global variable?
                $block->set('user', $shopper->_('user'));
                $block->set('shiptoAddresses', $shiptoAddresses);
                $block->set('shiptoAddressesSelected', $this->di->get('session')->get('shiptoAddress', 'invoice'));
                $block->set('loggendIn', 1);
            endif;
        else :
            $nextStep = $this->di->get('shop')->checkout->getNextStep();
            $logMessage .= ' user logged out';
            $formRegistration = new BaseForm();
            /** @var Datagroup $datagroup */
            $datagroup = Datagroup::findById($this->di->get('setting')->get('SHOP_DATAGROUP_REGISTRATIONFORM'));
            $datagroup->buildItemForm($formRegistration);
            $formRegistration->_('submit', '%FORM_SUBMIT% %CORE_AND% %CORE_TO% ' . strtolower($nextStep->_('name')));

            $formLogin = new LoginForm();

            $block->set('registrationForm', $formRegistration->renderForm('shop/checkout/register/'));
            $block->set('loginForm', $formLogin->renderForm('user/login'));
            $block->set('loggendIn', 0);
        endif;

        $this->di->get('log')->write(
            new ObjectID($this->view->getCurrentId()),
            Item::class,
            $logMessage
        );
    }
}
