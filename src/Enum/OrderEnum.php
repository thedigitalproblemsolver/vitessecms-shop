<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Enum;

enum OrderEnum: string
{
    case LISTENER = 'OrderListener';
    case GET_REPOSITORY = 'OrderListener:getRepository';
}