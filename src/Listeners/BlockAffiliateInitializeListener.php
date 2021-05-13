<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners;

use Phalcon\Events\Event;
use VitesseCms\Block\Forms\BlockForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;

class BlockAffiliateInitializeListener
{
    public function buildBlockForm(Event $event, BlockForm $form): void
    {
        $form->addDropdown(
            'Entry point datagroups',
            'datagroups',
            (new Attributes())->setMultiple(true)
                ->setInputClass('select2')
                ->setOptions(
                    ElementHelper::modelIteratorToOptions($form->di->get('repositories')->datagroup->findAll()))
        )
        ->addNumber('Cookie lifetime in days', 'cookieLifetime');
    }
}
