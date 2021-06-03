<?php declare(strict_types=1);

namespace VitesseCms\Shop\Fields;

use VitesseCms\Content\Models\Item;
use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Datafield\AbstractField;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\Enum\DiscountEnum;
use VitesseCms\Shop\Models\Discount;
use VitesseCms\Shop\Utils\PriceUtil;
use Phalcon\Di;

class ShopPrice extends AbstractField
{
    public static function beforeMaincontent(Item $item, Datafield $datafield): void
    {
        $item->set(
            $datafield->getCallingName() . '_saleDisplay',
            PriceUtil::formatDisplay(
                (float)$item->_($datafield->getCallingName() . '_sale')
            )
        );

        $item->set(
            $datafield->getCallingName() . 'Display',
            PriceUtil::formatDisplay(
                (float)$item->_($datafield->getCallingName())
            )
        );

        Di::getDefault()->get('eventsManager')->fire('discount:prepareItem', $item);
    }

    //TODO move to listener
    public function buildItemFormElement(
        AbstractForm $form,
        Datafield $datafield,
        Attributes $attributes,
        AbstractCollection $data = null
    )
    {
        $form->addText(
            $datafield->getNameField() . ' - ex. VAT',
            $datafield->getCallingName(),
            (new Attributes())->setReadonly()
        )->addNumber(
            $datafield->getNameField() . ' - purchase',
            $datafield->getCallingName() . '_purchase',
            (new Attributes())->setMin(0)
        )->addNumber(
            $datafield->getNameField() . ' - inc. VAT',
            $datafield->getCallingName() . '_sale',
            (new Attributes())->setMin(0)
        );

        Discount::setFindValue('target', ['$in' => [DiscountEnum::TARGET_PRODUCT, DiscountEnum::TARGET_FREE_SHIPPING]]);
        $form->addDropdown(
            'Discount',
            'discount',
            (new Attributes())->setInputClass('select2')
                ->setMultiple()
                ->setOptions(ElementHelper::arrayToSelectOptions(Discount::findAll()))
        );
    }
}
