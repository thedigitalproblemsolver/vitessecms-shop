<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Enum;

enum TaxRateEnum: string
{
    case LISTENER = 'TaxRateListener';
    case GET_REPOSITORY = 'TaxRateListener:getRepository';
}