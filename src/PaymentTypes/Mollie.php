<?php

declare(strict_types=1);

namespace VitesseCms\Shop\PaymentTypes;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Types\PaymentStatus;
use Phalcon\Di\Di;
use Phalcon\Events\Manager;
use stdClass;
use VitesseCms\Core\Enum\UrlEnum;
use VitesseCms\Core\Services\UrlService;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Log\Enums\LogServiceEnum;
use VitesseCms\Log\Services\LogService;
use VitesseCms\Setting\Enum\SettingEnum;
use VitesseCms\Setting\Services\SettingService;
use VitesseCms\Shop\AbstractPaymentType;
use VitesseCms\Shop\Enum\PaymentEnum;
use VitesseCms\Shop\Models\Order;
use VitesseCms\Shop\Models\Payment;

class Mollie extends AbstractPaymentType
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
        $this->urlService = $this->eventsManager->fire(UrlEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->logService = $this->eventsManager->fire(LogServiceEnum::ATTACH_SERVICE_LISTENER->value, new stdClass());
    }

    public function buildAdminForm(AbstractForm $form): void
    {
        $form->addText('API-key', 'apiKey', (new Attributes())->setRequired());
    }

    public function prepareOrder(Order $order): void
    {
    }

    public function doPayment(Order $order, Payment $payment): void
    {
        $molliePayment = $this->getClient($payment->apiKey)->payments->create($this->buildOrderRequest($order));
        $molliePayment->orderId = $order->orderId;

        $this->logService->write(
            $order->getId(),
            Order::class,
            'Order ' . $order->orderId . ' user redirected to Mollie'
        );

        $paymentType = $order->_('paymentType');
        $paymentType->transactionId = $molliePayment->id;
        $order->set('paymentType', $paymentType);
        $order->save();

        header('Location: ' . $molliePayment->getCheckoutUrl(), true, 303);
        die();
    }

    private function getClient(string $apiKey): MollieApiClient
    {
        return (new MollieApiClient())->setApiKey($apiKey);
    }

    private function buildOrderRequest(Order $order): array
    {
        return [
            'amount' => [
                'currency' => 'EUR',
                'value' => (string)round($order->getTotal(), 2)
            ],
            'description' => $this->getDescription($order),
            'redirectUrl' => $this->urlService->getBaseUri() . 'shop/payment/redirect/' . $order->getId(),
            'webhookUrl'  => $this->urlService->getBaseUri() . 'shop/payment/process/' . $order->getId(),
        ];
    }

    private function getDescription(Order $order): string
    {
        return 'Your order ' . $order->orderId . ' at ' . $this->settingService->getString('WEBSITE_DEFAULT_NAME');
    }

    public function getTransactionState(string $transactionId, Payment $payment): string
    {
        $molliePayment = $this->getClient($payment->apiKey)->payments->get($transactionId);

        return match ($molliePayment->status) {
            PaymentStatus::STATUS_PAID => PaymentEnum::PAID,
            PaymentStatus::STATUS_FAILED, PaymentStatus::STATUS_EXPIRED => PaymentEnum::ERROR,
            PaymentStatus::STATUS_CANCELED => PaymentEnum::CANCELLED,
            default => PaymentEnum::PENDING,
        };
    }
}