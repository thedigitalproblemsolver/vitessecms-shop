<?php declare(strict_types=1);

namespace VitesseCms\Shop\Blocks;

use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Block\AbstractBlockModel;
use VitesseCms\Block\Models\Block;
use VitesseCms\Shop\Enum\OrderStateEnum;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Models\OrderState;

class ShopPaymentResult extends AbstractBlockModel
{
    public function initialize()
    {
        parent::initialize();
        if (
            (
                !$this->di->user->isLoggedIn()
                || !$this->di->session->get('currentOrderId')
            ) && !AdminUtil::isAdminPage()
        ) :
            $this->di->flash->setError('USER_NO_ACCESS');
            $this->di->response->redirect($this->di->url->getBaseUri());
        endif;

        if ($this->di->user->isLoggedIn() && !AdminUtil::isAdminPage()) :
            Order::setFindPublished(false);
            $order = Order::findById($this->di->session->get('currentOrderId'));
            if ($order && (string)$this->di->user->getId() !== $order->_('shopper')['userId']) :
                $this->di->flash->setError('USER_NO_ACCESS');
                $this->di->response->redirect($this->di->url->getBaseUri());
            endif;
        endif;

        $this->excludeFromCache = true;
    }

    public function parse(Block $block): void
    {
        parent::parse($block);

        if ($this->di->session->get('currentOrderId')) :
            Order::setFindPublished(false);
            $order = Order::findById($this->di->session->get('currentOrderId'));
            $orderState = OrderState::findById($order->_('orderState')['_id']);
            $block->set('orderState', $orderState);
            $block->set('order', $order);
            foreach ((array)$orderState->_('analyticsTriggers') as $trigger) :
                switch ($trigger):
                    case OrderStateEnum::ANALYTICS_TRIGGER_MAILCHIMP:
                        if ($this->di->session->get('mailchimpCampaignId')) :
                            //$this->di->mailchimp->addOrder($order, $this->di->session->get('mailchimpCampaignId'));
                        endif;
                        break;
                    default:
                        $block->set('trigger' . ucfirst($trigger), true);
                endswitch;
            endforeach;

            $this->di->log->write(
                $order->getId(),
                Order::class,
                'Order ' . $order->_('orderId') . ' thankyou with orderstate ' . $orderState->_('calling_name')
            );
        else :
            $this->di->flash->error('Order could not be found');
        endif;
    }
}
