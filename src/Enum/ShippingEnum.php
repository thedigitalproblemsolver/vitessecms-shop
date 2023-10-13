<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Enum;

enum ShippingEnum: string
{
    case PACKAGE = 'package';
    case ENVELOPE = 'envelope';
    case LISTENER = 'ShippingListener';
    case GET_REPOSITORY = 'ShippingListener:getRepository';
}
