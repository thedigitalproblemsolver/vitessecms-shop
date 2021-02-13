<?php declare(strict_types=1);

namespace VitesseCms\Shop\Models;

use VitesseCms\Database\AbstractCollection;

class Ean extends AbstractCollection
{
    /**
     * @var string
     */
    public $parentItem;

    public function getParentItem(): ?string
    {
        if(empty($this->parentItem)) :
            return null;
        endif;

        return $this->parentItem;
    }
}
