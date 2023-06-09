<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Plugin\AdminNotification\Block;
use Magento\AdminNotification\Block\ToolbarEntry as NativeToolbarEntry;
/**
 * Add html attributes to magerubik notifications
 */
class ToolbarEntry
{
    const MAGERUBIK_ATTRIBUTE = ' data-mrall-logo="1"';
    public function afterToHtml(
        NativeToolbarEntry $subject,
        $html
    ) {
        $collection = $subject->getLatestUnreadNotifications()
            ->clear()
            ->addFieldToFilter('is_magerubik', 1);
        foreach ($collection as $item) {
            $search = 'data-notification-id="' . $item->getId() . '"';
            if ($item->getData('image_url')) {
                $html = str_replace(
                    $search,
                    $search . ' style='
                    . '"background: url(' . $item->getData('image_url') . ') no-repeat 5px 7px; background-size: 30px;"'
                    . self::MAGERUBIK_ATTRIBUTE,
                    $html
                );
            } else {
                $html = str_replace($search, $search . self::MAGERUBIK_ATTRIBUTE, $html);
            }
        }
        return $html;
    }
}
