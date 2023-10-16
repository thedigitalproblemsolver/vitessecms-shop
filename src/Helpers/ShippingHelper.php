<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Helpers;

use VitesseCms\Core\Utils\DirectoryUtil;
use VitesseCms\Core\Utils\FileUtil;
use VitesseCms\Core\Utils\SystemUtil;

final class ShippingHelper
{
    public static function getTypes(string $vendorDir, string $accountDir): array
    {
        $types = [];
        $shippingFiles = DirectoryUtil::getFilelist($vendorDir . 'shop/src/ShippingTypes/');
        $shippingFilesAccount = DirectoryUtil::getFilelist($accountDir . '/src/shop/ShippingTypes/');
        $files = array_merge($shippingFilesAccount, $shippingFiles);
        foreach ($files as $path => $file) :
            $name = FileUtil::getName($file);
            $types[SystemUtil::createNamespaceFromPath($path)] = $name;
        endforeach;

        return $types;
    }
}
