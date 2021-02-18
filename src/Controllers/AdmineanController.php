<?php declare(strict_types=1);

namespace VitesseCms\Shop\Controllers;

use VitesseCms\Admin\AbstractAdminController;
use VitesseCms\Content\Models\Item;
use VitesseCms\Core\Helpers\ItemHelper;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Shop\Factories\EanFactory;
use VitesseCms\Shop\Forms\EanForm;
use VitesseCms\Shop\Interfaces\RepositoriesInterface;
use VitesseCms\Shop\Models\Ean;

class AdmineanController extends AbstractAdminController implements RepositoriesInterface
{
    public function onConstruct()
    {
        parent::onConstruct();

        $this->class = Ean::class;
        $this->classForm = EanForm::class;
    }

    public function searchAction(): void
    {
        $result = ['items' => []];

        if ($this->request->isAjax() && \strlen($this->request->get('search')) > 1) :
            $items = $this->repositories->item->findAll(
                new FindValueIterator([
                    new FindValue(
                        'name.'.$this->configuration->getLanguageShort(),
                        $this->request->get('search'),
                        'like'
                    ),
                    new FindValue('ean', ['$nin' => [null, '']])
                ])
            );

            if ($items) :
                while ($items->valid()) :
                    $item = $items->current();
                    $path = ItemHelper::getPathFromRoot($item);
                    $result['items'][] = [
                        'id'   => (string)$item->getId(),
                        'name' => implode(' - ', $path),
                    ];
                    $items->next();
                endwhile;
            endif;
        endif;

        $this->prepareJson($result);
    }

    public function reloadAction(): void
    {
        Item::setFindValue('ean', ['$nin' => [null, '']]);
        $items = Item::findAll();
        foreach ($items as $item):
            Ean::setFindValue('name', (string)$item->_('ean'));
            Ean::setFindPublished(false);
            if (Ean::count() === 0):
                EanFactory::create(
                    (string)$item->_('ean'),
                    (string)$item->getId(),
                    '',
                    true
                )->save();
            endif;

            if (\is_array($item->_('variations'))) :
                foreach ($item->_('variations') as $variation) :
                    if (!empty($variation['ean']) && !empty($variation['sku'])) :
                        Ean::setFindValue('name', $variation['ean']);
                        Ean::setFindPublished(false);
                        if (Ean::count() === 0):
                            EanFactory::create(
                                $variation['ean'],
                                (string)$item->getId(),
                                $variation['sku'],
                                true
                            )->save();
                        endif;
                    endif;
                endforeach;
            endif;
        endforeach;

        $this->redirect();
    }
}
