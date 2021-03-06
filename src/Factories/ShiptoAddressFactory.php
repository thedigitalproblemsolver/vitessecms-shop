<?php declare(strict_types=1);

namespace VitesseCms\Shop\Factories;

use VitesseCms\Content\Models\Item;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Setting\Services\SettingService;
use VitesseCms\Shop\Models\ShiptoAddress;
use VitesseCms\User\Models\User;

class ShiptoAddressFactory
{
    public static function createFromDatagroup(SettingService $setting): BaseObjectInterface
    {
        $shiptoAddress = new Item();
        $shiptoAddress->set('datagroup', $setting->get('SHOP_DATAGROUP_SHOPPERSHIPTO'));

        return $shiptoAddress;
    }

    public static function createFromUser(
        User $user,
        Datagroup $datagroup,
        DatafieldRepository $datafieldRepository
    ): ShiptoAddress
    {
        $shipToAddress = new ShiptoAddress();
        foreach ($datagroup->getDatafields() as $datafield):
            $datafieldModel = $datafieldRepository->getById($datafield['id']);
            if ($datafieldModel !== null):
                $shipToAddress->set(
                    $datafieldModel->getCallingName(),
                    $user->_($datafieldModel->getCallingName())
                );
            endif;
        endforeach;
        $shipToAddress->set('userId', (string)$user->getId());

        return $shipToAddress;
    }

    public static function createFromItem(
        Item $item,
        Datagroup $datagroup,
        DatafieldRepository $datafieldRepository
    ): ShiptoAddress
    {
        $shipToAddress = new ShiptoAddress();
        foreach ($datagroup->getDatafields() as $datafield):
            $datafieldModel = $datafieldRepository->getById($datafield['id']);
            if ($datafieldModel !== null):
                $shipToAddress->set(
                    $datafieldModel->getCallingName(),
                    $item->_($datafieldModel->getCallingName())
                );
            endif;
        endforeach;

        return $shipToAddress;
    }

    public static function createFromOrderArray(array $shipToAddressArray): ShiptoAddress
    {
        return (new ShiptoAddress())
            ->setCountryId($shipToAddressArray['country']);
    }
}
