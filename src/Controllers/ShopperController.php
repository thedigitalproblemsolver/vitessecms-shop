<?php

declare(strict_types=1);

namespace VitesseCms\Shop\Controllers;

use MongoDB\BSON\ObjectID;
use stdClass;
use VitesseCms\Communication\Helpers\CommunicationHelper;
use VitesseCms\Content\Models\Item;
use VitesseCms\Core\AbstractController;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Form\Forms\BaseForm;
use VitesseCms\Shop\Enum\CountryEnum;
use VitesseCms\Shop\Factories\ShiptoAddressFactory;
use VitesseCms\Shop\Models\Shopper;
use VitesseCms\Shop\Repositories\CountryRepository;
use VitesseCms\User\Models\User;

final class ShopperController extends AbstractController
{
    private CountryRepository $countryRepository;

    public function onConstruct()
    {
        $this->countryRepository = $this->eventsManager->fire(CountryEnum::GET_REPOSITORY->value, new stdClass());
    }

    public function editAction(): void
    {
        Shopper::setFindValue('userId', (string)$this->user->getId());
        /** @var Shopper $shopper */
        $shopper = Shopper::findFirst();

        if (!$shopper) :
            $shopper = new Shopper();
            $shopper->set('user', $this->user);
        endif;

        $form = new BaseForm();
        $datagroup = $this->getEditForm();
        $datagroup->buildItemForm($form, $shopper);
        $form->addSubmitButton('save');

        $this->view->setVar('content', $form->renderForm('shop/shopper/save/'));
        $this->prepareView();
    }

    public function getEditForm(): Datagroup
    {
        /** @var Datagroup $datagroup */
        $datagroup = Datagroup::findById($this->setting->get('SHOP_DATAGROUP_REGISTRATIONFORM'));
        $datagroup->addExcludeField('password');
        $datagroup->addExcludeField('password2');
        $datagroup->addExcludeField('refferer');
        $datagroup->addExcludeField('agreedTerms');

        return $datagroup;
    }

    public function saveAction(): void
    {
        $form = new BaseForm();
        $datagroup = $this->getEditForm();
        $datagroup->buildItemForm($form);
        if ($form->validate($this)) :

            $post = $this->request->getPost();
            $post['email'] = strtolower($post['email']);
            User::setFindValue('email', $post['email']);
            /** @var User $user */
            $user = User::findFirst();

            Shopper::setFindValue('userId', (string)$this->user->getId());
            /** @var Shopper $shopper */
            $shopper = Shopper::findFirst();

            if (!$shopper) :
                Shopper::createNew($post, $user);
                try {
                    CommunicationHelper::sendSystemEmail($user->_('email'), 'custom', 'shopshoppersave');
                } catch (Exception $e) {
                }
            endif;

            $user->addPersonalInformation($post);
            $user->save();
            $shopper->set('user', $user);
            $shopper->addShopperInformation($post);
            $shopper->save();

            $this->flash->setSucces('USER_INFORMATION_CHANGED_SUCCESS');
        endif;

        $this->redirect();
    }

    public function editShipToAction(): void
    {
        $shiptoAddress = ShiptoAddressFactory::createFromDatagroup($this->setting);
        $datagroupId = $this->setting->get('SHOP_DATAGROUP_SHOPPERSHIPTO');
        if ($this->request->get('id') !== null) :
            Item::setFindValue('_id', new ObjectID($this->request->get('id')));
            Item::setFindValue('datagroup', $datagroupId);
            $shiptoAddress = Item::findFirst();
        endif;
        $shiptoAddress->set('userId', (string)$this->user->getId());

        $form = new BaseForm();
        /** @var Datagroup $datagroup */
        $datagroup = Datagroup::findById($datagroupId);
        $datagroup->buildItemForm($form, $shiptoAddress);
        $form->addSubmitButton('save');

        $this->view->setVar('content', $form->renderForm('shop/shopper/saveShipTo/'));
        $this->prepareView();
    }

    public function saveShipToAction(): void
    {
        $datagroupId = $this->setting->get('SHOP_DATAGROUP_SHOPPERSHIPTO');
        $form = new BaseForm();
        /** @var Datagroup $datagroup */
        $datagroup = Datagroup::findById($datagroupId);
        $datagroup->buildItemForm($form);
        $redirectUrl = $this->request->getHTTPReferer();
        if ($form->validate($this)) :
            $post = $this->request->getPost();
            unset($post['csrf']);
            $shiptoAddress = ShiptoAddressFactory::createFromDatagroup($this->setting);
            if (isset($post['id']) && !empty($post['id'])) :
                Item::setFindValue('_id', new ObjectID($post['id']));
                Item::setFindValue('datagroup', $datagroupId);
                $shiptoAddress = Item::findFirst();
            endif;
            $shiptoAddress->bind($post);
            $country = $this->countryRepository->getById($post['country']);
            $shiptoAddress->set('countryName', $country->getRaw('name'));
            $shiptoAddress->set('published', true);
            $shiptoAddress->save();
            $redirectUrl = $this->url->addParamsToQuery(
                'id',
                (string)$shiptoAddress->getId(),
                $this->request->getHTTPReferer()
            );

            $this->flash->setSucces('SHOP_SHIPTO_SAVED_SUCCESS');
        endif;

        $this->redirect($redirectUrl);
    }
}
