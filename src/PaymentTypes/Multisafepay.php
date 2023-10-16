<?php

declare(strict_types=1);

namespace VitesseCms\Shop\PaymentTypes;

use MultiSafepay\Api\Transactions\OrderRequest;
use MultiSafepay\Api\Transactions\OrderRequest\Arguments\CustomerDetails;
use MultiSafepay\Api\Transactions\OrderRequest\Arguments\PaymentOptions;
use MultiSafepay\Api\Transactions\OrderRequest\Arguments\PluginDetails;
use MultiSafepay\Sdk;
use MultiSafepay\ValueObject\Customer\Address;
use MultiSafepay\ValueObject\Customer\Country;
use MultiSafepay\ValueObject\Customer\EmailAddress;
use MultiSafepay\ValueObject\Customer\PhoneNumber;
use MultiSafepay\ValueObject\Money;
use Phalcon\Di\Di;
use Phalcon\Events\Manager;
use stdClass;
use VitesseCms\Configuration\Enums\ConfigurationEnum;
use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Core\Enum\EnvEnum;
use VitesseCms\Core\Enum\UrlEnum;
use VitesseCms\Core\Services\UrlService;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Log\Enums\LogServiceEnum;
use VitesseCms\Log\Services\LogService;
use VitesseCms\Setting\Enum\SettingEnum;
use VitesseCms\Setting\Services\SettingService;
use VitesseCms\Shop\AbstractPaymentType;
use VitesseCms\Shop\Enum\CountryEnum;
use VitesseCms\Shop\Enum\PaymentEnum;
use VitesseCms\Shop\Helpers\CartHelper;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Models\Payment;
use VitesseCms\Shop\Repositories\CountryRepository;

class Multisafepay extends AbstractPaymentType
{
    private readonly SettingService $settingService;
    private readonly ConfigService $configService;
    private readonly UrlService $urlService;
    private readonly CountryRepository $countryRepository;
    private readonly LogService $logService;
    private readonly Manager $eventsManager;

    public function __construct()
    {
        $this->eventsManager = Di::getDefault()->get('eventsManager');

        $this->settingService = $this->eventsManager->fire(SettingEnum::ATTACH_SERVICE_LISTENER->value, new stdClass());
        $this->configService = $this->eventsManager->fire(
            ConfigurationEnum::ATTACH_SERVICE_LISTENER->value,
            new stdClass()
        );
        $this->urlService = $this->eventsManager->fire(UrlEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->countryRepository = $this->eventsManager->fire(CountryEnum::GET_REPOSITORY->value, new stdClass());
        $this->logService = $this->eventsManager->fire(LogServiceEnum::ATTACH_SERVICE_LISTENER->value, new stdClass());
    }

    public function buildAdminForm(AbstractForm $form): void
    {
        $form->addText('Account ID', 'accountId', (new Attributes())->setRequired())
            ->addText('API-key', 'apiKey', (new Attributes())->setRequired());
    }

    public function prepareOrder(Order $order): void
    {
        if (
            !isset($order->_('paymentType')['transactionId'])
            && Di::getDefault()->get('request')->get('transactionid')
        ) :
            $paymentType = $order->_('paymentType');
            $paymentType['transactionId'] = Di::getDefault()->get('request')->get('transactionid');
            $order->set('paymentType', $paymentType);
        endif;
    }

    public function doPayment(Order $order, Payment $payment): void
    {
        $multiSafepaySdk = $this->getMultiSafepaySdk($payment);
        $transactionManager = $multiSafepaySdk->getTransactionManager()->create(
            $this->buildOrderRequest($order)
        );

        $this->logService->write(
            $order->getId(),
            Order::class,
            'Order ' . $order->orderId . ' user redirected to Multisafepay'
        );

        header('Location: ' . $transactionManager->getPaymentUrl());
        die();
    }

    private function getMultiSafepaySdk(Payment $payment): Sdk
    {
        return new Sdk(
            $payment->getString('apiKey'),
            getenv(EnvEnum::ENVIRONMENT) === EnvEnum::ENVIRONMENT_PRODUCTION
        );
    }

    private function buildOrderRequest(Order $order): OrderRequest
    {
        return (new OrderRequest())
            ->addType('redirect')
            ->addOrderId((string)$order->orderId)
            ->addDescriptionText($this->getDescription($order))
            ->addMoney(new Money(round($order->getTotal(), 2) * 100))
            ->addGatewayCode('IDEAL')
            ->addCustomer($this->buildCustomer($order))
            ->addDelivery($this->buildCustomer($order))
            ->addPluginDetails($this->buildPluginDetails())
            ->addPaymentOptions($this->buildPaymentOptions($order));
    }

    private function getDescription(Order $order): string
    {
        return 'Your order ' . $order->orderId . ' at ' . $this->settingService->getString('WEBSITE_DEFAULT_NAME');
    }

    private function buildCustomer(Order $order): CustomerDetails
    {
        return (new CustomerDetails())
            ->addFirstName($order->shopper->user->firstName)
            ->addLastName($order->shopper->user->lastName)
            ->addAddress($this->buildAddress($order))
            ->addEmailAddress(new EmailAddress($order->shopper->user->email))
            ->addPhoneNumber(new PhoneNumber($order->shopper->user->phoneNumber))
            ->addLocale($this->configService->getLanguageLocale());
    }

    private function buildAddress(Order $order): Address
    {
        $country = $this->countryRepository->getById($order->shopper->user->country);

        return (new Address())
            ->addStreetName($order->shopper->user->street)
            ->addStreetNameAdditional('')
            ->addHouseNumber($order->shopper->user->houseNumber)
            ->addZipCode($order->shopper->user->zipCode)
            ->addCity($order->shopper->user->city)
            ->addCountry(new Country($country->short));
    }

    private function buildPluginDetails(): PluginDetails
    {
        return (new PluginDetails())
            ->addApplicationName('Shop ' . $this->settingService->getString('WEBSITE_DEFAULT_NAME'))
            ->addApplicationVersion('1.0.0')
            ->addPluginVersion('1.0.0');
    }

    private function buildPaymentOptions(Order $order): PaymentOptions
    {
        return (new PaymentOptions())
            ->addNotificationUrl($this->urlService->getBaseUri() . 'shop/payment/process/' . $order->getId())
            ->addRedirectUrl($this->urlService->getBaseUri() . 'shop/payment/redirect/' . $order->getId())
            ->addCancelUrl($this->urlService->getBaseUri() . 'shop/payment/cancel/' . $order->getId())
            ->addCloseWindow(true);
    }

    public function getTransactionState(string $transactionId, Payment $payment): string
    {
        $multiSafepaySdk = $this->getMultiSafepaySdk($payment);
        $transaction = $multiSafepaySdk->getTransactionManager()->get($transactionId);

        if ($transaction->getPaymentDetails()->getType() === 'BANKTRANS'):
            return PaymentEnum::BANKTRANSFER;
        endif;

        return match ($transaction->getStatus()) {
            'completed', 'refunded' => PaymentEnum::PAID,
            'void', 'declined', 'expired' => PaymentEnum::ERROR,
            'canceled' => PaymentEnum::CANCELLED,
            default => PaymentEnum::PENDING,
        };
    }

    protected function buildItemlist(array $items): string
    {
        $item_list = '';
        foreach ($items as $item) :
            $item_list .= $item->_('quantity') . " x " . CartHelper::getLogNameFromItem($item) . "\n";
        endforeach;

        return $item_list;
    }
}