<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Enum;

enum ShopperEnum: string
{
    case LISTENER = 'ShopperListener';
    case GET_REPOSITORY = 'ShopperListener:getRepository';
}