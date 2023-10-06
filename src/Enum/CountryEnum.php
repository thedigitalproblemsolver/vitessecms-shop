<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Enum;

enum CountryEnum: string
{
    case LISTENER = 'CountryListener';
    case GET_REPOSITORY = 'CountryListener:getRepository';
}