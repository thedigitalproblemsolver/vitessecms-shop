<?php declare(strict_types=1);

namespace VitesseCms\Shop\Factories;

use VitesseCms\Core\AbstractFactory;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Core\Interfaces\FactoryInterface;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Shop\Models\Shopper;
use VitesseCms\User\Models\User;

class ShopperFactory extends AbstractFactory implements FactoryInterface
{
    public static function create(BaseObjectInterface $bindData = null): BaseObjectInterface
    {
        $shopper = parent::createCollection(Shopper::class);

        return $shopper;
    }

    public static function createFromUser(User $user, array $data = []): Shopper
    {
        $shopper = new Shopper();
        //$shopper->set('userId', (string)$user->getId());
        $shopper->set('user', $user);
        $shopper->addShopperInformation($data);
        $shopper->set('published', true);

        return $shopper;
    }

    public static function bindByDatagroup(Datagroup $datagroup, array $data, Shopper $shopper, DatafieldRepository $datafieldRepository)
    {
        foreach ($datagroup->getDatafields() as $field) :
            $datafield = $datafieldRepository->getById($field['id']);
            if ($datafield !== null) :
                if (isset($data[$datafield->getCallingName()])):
                    $shopper->set($datafield->getCallingName(), $data[$datafield->getCallingName()]);
                endif;

                if (isset($data['BSON_' . $datafield->getCallingName()])) :
                    $shopper->set('BSON_' . $datafield->getCallingName(), $data['BSON_' . $datafield->getCallingName()]);
                endif;
            endif;
        endforeach;
    }
}
