<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Enum;

use VitesseCms\Core\AbstractEnum;

class PaymentEnum extends AbstractEnum
{
    public const PAID = 'PAID';
    public const ERROR = 'ERROR';
    public const CANCELLED = 'CANCELLED';
    public const PENDING = 'PENDING';
    public const BANKTRANSFER = 'BANKTRANSFER';

    public const LISTENER = 'paymentListener';
    public const GET_REPOSITORY = 'paymentListener:getRepository';
}
