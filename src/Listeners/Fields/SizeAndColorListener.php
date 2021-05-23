<?php declare(strict_types=1);

namespace VitesseCms\Shop\Listeners\Fields;

use Phalcon\Events\Event;
use Phalcon\Http\Request;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Shop\Enums\SizeAndColorEnum;

class SizeAndColorListener
{
    public function beforeItemSave(Event $event, AbstractCollection $item, Datafield $datafield): void
    {
        $request = new Request();
        if ($request->get($datafield->getCallingName())) :
            $variations = [];
            $images = $request->get($datafield->getCallingName() . '_images');
            foreach ((array)$request->get($datafield->getCallingName()) as $key => $variation) :
                if ($key !== '__key__') :
                    $variation['image'] = (array)$images[strtolower($variation['color'])];
                    foreach ($variation['image'] as $imageKey => $value) :
                        if (empty($value)) :
                            unset($variation['image'][$imageKey]);
                        endif;
                    endforeach;
                    $variations[$variation['sku']] = $variation;
                endif;
            endforeach;
            $item->set($datafield->getCallingName(), $variations);
        endif;

        $sizes = $colors = [];
        $inStock = 0;
        $firstImage = $item->_('image');
        $firstImageSet = false;
        if (is_array($item->_($datafield->getCallingName()))) :
            foreach ($item->_($datafield->getCallingName()) as $variation) :
                if ((int)$variation['stock'] > 0) :
                    if (!isset($colors[$variation['color']])) :
                        $colors[$variation['color']] = ['sku' => []];
                    endif;

                    if (!isset($sizes[$variation['size']])) :
                        $sizes[$variation['size']] = ['sku' => []];
                    endif;
                    $sizes[$variation['size']]['sku'][] = $variation['sku'];
                    $colors[strtolower($variation['color'])]['sku'][] = $variation['sku'];
                    $colors[strtolower($variation['color'])]['image'] = $variation['image'];

                    $inStock += (int)$variation['stock'];
                    if (!$firstImageSet && !empty($variation['image'][0])) :
                        $firstImage = $variation['image'][0];
                        $firstImageSet = true;
                    endif;
                endif;
            endforeach;

            $aColors = [];
            foreach ($colors as $s => $sku) :
                $aColors[] = [
                    'color' => $s,
                    'sku' => implode(',', $sku['sku']),
                    'image' => implode(',', $sku['image']),
                    'colorClass' => str_replace('#', '', $s),
                ];
            endforeach;

            $aSizes = [];
            foreach (SizeAndColorEnum::sizes as $size => $sizeName) :
                if (isset($sizes[$size])) :
                    $aSizes[] = [
                        'size' => $size,
                        'sku' => implode(',', $sizes[$size]['sku']),
                    ];
                endif;
            endforeach;

            $item->set($datafield->getCallingName() . 'Template', [
                'colors' => $aColors,
                'sizes' => $aSizes,
            ]);

            if ($inStock === 0) :
                $item->set('outOfStock', true);
            endif;

            if ($item->_('outOfStock')) :
                $item->set('isFilterable', false);
            endif;

            $item->set('firstImage', $firstImage);
        endif;
    }
}