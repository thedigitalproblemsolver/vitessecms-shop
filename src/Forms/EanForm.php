<?php declare(strict_types=1);

namespace VitesseCms\Shop\Forms;

use Phalcon\Tag;
use VitesseCms\Core\Helpers\ItemHelper;
use VitesseCms\Form\AbstractFormWithRepository;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Interfaces\FormWithRepositoryInterface;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\Models\Ean;
use VitesseCms\Shop\Repositories\RepositoryCollection;

class EanForm extends AbstractFormWithRepository
{
    /**
     * @var Ean
     */
    protected $_entity;

    /**
     * @var RepositoryCollection
     */

    protected $repositories;

    public function buildForm(): FormWithRepositoryInterface
    {
        $html = '';
        $options = ['' => '%ADMIN_TYPE_TO_SEARCH%'];

        if ($this->_entity->getParentItem() !== null) :
            $selectedItem = $this->repositories->item->getById($this->_entity->getParentItem(), false);
            $itemPath = ItemHelper::getPathFromRoot($selectedItem);
            $options[(string)$selectedItem->getId()] = implode(' - ', $itemPath);

            $html = Tag::linkTo([
                'action' => $selectedItem->_('slug'),
                'text' => 'View item',
                'target' => '_new'
            ]);
        endif;

        $this->addText('%CORE_NAME%', 'name', (new Attributes())->setRequired())
            ->addDropdown(
                'Parent item',
                'parentItem',
                (new Attributes())
                    ->setInputClass('select2-ajax')
                    ->setDataUrl('/Admin/shop/adminean/search/')
                    ->setOptions(ElementHelper::arrayToSelectOptions($options)
                    ))
            ->addHtml($html)
            ->addText('SKU', 'sku', (new Attributes())->setRequired())
            ->addSubmitButton('%CORE_SAVE%');

        return $this;
    }
}
