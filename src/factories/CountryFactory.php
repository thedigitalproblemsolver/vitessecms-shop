<?php declare(strict_types=1);

namespace VitesseCms\Shop\Factories;

use VitesseCms\Shop\Models\Country;

class CountryFactory
{
    public static function create(string $name, string $short, string $shortThree, bool $published = false): Country {
        return (new Country())
            ->set('name', $name, true)
            ->set('short', $short)
            ->set('shortThree', $shortThree)
            ->set('published', $published);
    }
}
