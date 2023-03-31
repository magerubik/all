<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */


namespace Magerubik\All\Plugin\AdminNotification\Block\Grid\Renderer;

use Magento\AdminNotification\Block\Grid\Renderer\Notice as NativeNotice;

class Notice
{
    public function aroundRender(
        NativeNotice $subject,
        \Closure $proceed,
        \Magento\Framework\DataObject $row
    ) {
        $result = $proceed($row);

        $magerubikLogo = '';
        $magerubikImage = '';
        if ($row->getData('is_magerubik')) {
            if ($row->getData('image_url')) {
                $magerubikImage = ' style="background: url(' . $row->getData("image_url") . ') no-repeat;"';
            } else {
                $magerubikLogo = ' magerubik-grid-logo';
            }
        }
        $result = '<div class="mrall-grid-message' . $magerubikLogo . '"' . $magerubikImage . '>' . $result . '</div>';

        return  $result;
    }
}
