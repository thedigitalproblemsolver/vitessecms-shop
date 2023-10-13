<?php

declare(strict_types=1);

namespace VitesseCms\Shop;

use MongoDB\BSON\ObjectId;
use Phalcon\Di\Di;
use Phalcon\Events\Manager;
use Phalcon\Tag;
use stdClass;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Database\Utils\MongoUtil;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\Enum\CountryEnum;
use VitesseCms\Shop\Enum\DiscountEnum;
use VitesseCms\Shop\Enum\ShippingEnum;
use VitesseCms\Shop\Enum\ShopperEnum;
use VitesseCms\Shop\Enum\TaxRateEnum;
use VitesseCms\Shop\Forms\ShippingForm;
use VitesseCms\Shop\Helpers\CheckoutHelper;
use VitesseCms\Shop\Helpers\DiscountHelper;
use VitesseCms\Shop\Interfaces\ShippingTypeInterface;
use VitesseCms\Shop\Models\Discount;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Models\Shipping;
use VitesseCms\Shop\Repositories\CountryRepository;
use VitesseCms\Shop\Repositories\ShopperRepository;
use VitesseCms\Shop\Repositories\TaxRateRepository;
use VitesseCms\User\Models\User;

abstract class AbstractShippingType extends AbstractCollection implements
    ShippingTypeInterface
{
    public ?string $barcode;
    public ?array $countryCost;
    public ?array $defaultCountryCost;
    public string $countryCostVAT;
    protected ?Shipping $shipping;
    protected CountryRepository $countryRepository;
    protected TaxRateRepository $taxRateRepository;
    protected ShopperRepository $shopperRepository;
    protected Manager $eventsManager;
    protected User $currentUser;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->eventsManager = $this->getDI()->get('eventsManager');
        $this->currentUser = $this->getDI()->get('user');
        $this->countryRepository = $this->eventsManager->fire(CountryEnum::GET_REPOSITORY->value, new stdClass());
        $this->taxRateRepository = $this->eventsManager->fire(TaxRateEnum::GET_REPOSITORY->value, new stdClass());
        $this->shopperRepository = $this->eventsManager->fire(ShopperEnum::GET_REPOSITORY->value, new stdClass());
    }

    public function buildAdminForm(ShippingForm $form): void
    {
        $form->addDropdown(
            'Country Cost VAT',
            'countryCostVAT',
            (new Attributes())->setRequired()->setOptions(
                ElementHelper::modelIteratorToOptions($this->taxRateRepository->findAll())
            )
        );
        $form->addNumber(
            'Default Country cost',
            'countryCostDefault',
            (new Attributes())->setMin(0)->setStep(0.05)->setRequired()->setMultilang()
        );

        $countries = $this->countryRepository->findAll();
        $countryCost = $form->getEntity()->countryCost ?? [];
        while ($countries->valid()) {
            $country = $countries->current();
            $form->addNumber(
                $country->getNameField(),
                'countryCost[' . $country->getId() . ']',
                (new Attributes())->setMin(0)->setStep(0.05)->setDefaultValue(
                    isset($countryCost[(string)$country->getId()]) ? $countryCost[(string)$country->getId()] : '0'
                )->setRequired()
            );

            $countries->next();
        }
    }

    public function getLabelLink(Order $order): string
    {
        if (
            isset($order->_('orderState')['printShippingLabel'])
            && $order->_('orderState')['printShippingLabel'] === '1'
        ) :
            $link = 'admin/shop/adminshipping/shippingLabel/' . $order->getId();

            return Tag::linkTo([
                    'action' => $link . '?packageType=' . ShippingEnum::ENVELOPE->value,
                    'text' => '&nbsp;envelope label',
                    'class' => 'fa fa-envelope',
                    'target' => '_blank',
                ]) .
                '<br />' .
                Tag::linkTo([
                        'action' => $link . '?packageType=' . ShippingEnum::PACKAGE->value,
                        'text' => '&nbsp;package label',
                        'class' => 'fa fa-gift',
                        'target' => '_blank',
                    ]
                );
        endif;

        return '';
    }

    public function getLabel(Order $order, ?string $packageType): ?string
    {
        return '';
    }

    public function hasFreeShippingItems(array $items): bool
    {
        /** @var User $user */
        $user = Di::getDefault()->get('user');
        $shipToAddress = CheckoutHelper::getShiptoAddress($user);
        if (is_array($items['products'])) :
            foreach ($items['products'] as $product) :
                if (is_array($product->_('discount'))) :
                    $ids = [];
                    foreach ($product->_('discount') as $discountId) :
                        if (MongoUtil::isObjectId($discountId)) :
                            $ids[] = new ObjectId($discountId);
                        endif;
                    endforeach;
                    Discount::setFindValue('_id', ['$in' => $ids]);
                    Discount::setFindValue('target', DiscountEnum::TARGET_FREE_SHIPPING);
                    if (
                        $shipToAddress !== null
                        && $user->isLoggedIn()
                        && !empty($shipToAddress->_('country'))
                    ) :
                        $discounts = Discount::findAll();
                        foreach ($discounts as $discount):
                            if (in_array($shipToAddress->_('country'), $discount->_('countries'), true)) :
                                return true;
                            endif;
                        endforeach;
                    else :
                        if (Discount::count()):
                            return true;
                        endif;
                    endif;
                endif;
            endforeach;
        endif;

        return false;
    }

    public function getTrackAndTraceLink(Order $order): string
    {
        return '';
    }

    public function hasFreeShippingCodeForAll(): bool
    {
        /** @var Discount $discount */
        $discount = DiscountHelper::getFromSession(DiscountEnum::TARGET_FREE_SHIPPING);

        if ($discount && (new DiscountHelper())->isValid($discount)) :
            foreach ($discount->_('countries') as $country) :
                if ($country === 'all') {
                    return true;
                }
            endforeach;
        endif;

        return false;
    }

    public function getBarcode(): string
    {
        return $this->barcode ?? '';
    }
}
