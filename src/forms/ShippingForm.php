<?php declare(strict_types=1);

namespace VitesseCms\Shop\Forms;

use VitesseCms\Database\AbstractCollection;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\Helpers\ShippingHelper;
use VitesseCms\Shop\Models\Shipping;
use Phalcon\Di;

class ShippingForm extends AbstractForm
{
    public function initialize( Shipping $item = null)
    {
        if( $item === null) :
            $item = new Shipping();
        endif;

        $this->addText('%CORE_NAME%', 'name',(new Attributes())->setRequired()->setMultilang());

        if( !$item->_('type') ) :
            $this->addDropdown(
                '%ADMIN_TYPE%',
                'type',
                (new Attributes())->setRequired()
                    ->setOptions(ElementHelper::arrayToSelectOptions(ShippingHelper::getTypes(
                        $this->configuration->getRootDir(),
                        $this->configuration->getAccount()
                    )
                )
            ));
        else :
            $object = ShippingHelper::getClass($item->_('type'));
            /** @var AbstractCollection $item */
            $item = new $object();
            $item->buildAdminForm($this);
        endif;

        $this->addSubmitButton('%CORE_SAVE%');
    }
}
