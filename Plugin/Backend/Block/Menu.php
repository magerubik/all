<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */


namespace Magerubik\All\Plugin\Backend\Block;

use Magento\Backend\Block\Menu as NativeMenu;

class Menu
{
    const MAX_ITEMS = 300;

    /**
     * @param NativeMenu $subject
     * @param $menu
     * @param int $level
     * @param int $limit
     * @param array $colBrakes
     *
     * @return array
     */
    public function beforeRenderNavigation(NativeMenu $subject, $menu, $level = 0, $limit = 0, $colBrakes = [])
    {
        if ($level !== 0 && $menu->get('Magerubik_All::marketplace')) {
            $level = 0;
            $limit = self::MAX_ITEMS;
            if (is_array($colBrakes)) {
                foreach ($colBrakes as $key => $colBrake) {
                    if (isset($colBrake['colbrake'])
                        && $colBrake['colbrake']
                    ) {
                        $colBrakes[$key]['colbrake'] = false;
                    }

                    if (isset($colBrake['colbrake']) && (($key - 1) % $limit) === 0) {
                        $colBrakes[$key]['colbrake'] = true;
                    }
                }
            }
        }

        return [$menu, $level, $limit, $colBrakes];
    }

    /**
     * @param NativeMenu $subject
     * @param string     $html
     *
     * @return string
     */
    public function afterToHtml(NativeMenu $subject, $html)
    {
        $js = $subject->getLayout()->createBlock(\Magento\Backend\Block\Template::class)
            ->setTemplate('Magerubik_All::js.phtml')
            ->toHtml();

        return $html . $js;
    }
}
