<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Model\Source;
class NotificationType implements \Magento\Framework\Option\ArrayInterface
{
    const GENERAL = 'INFO';
    const SPECIAL_DEALS = 'PROMO';
    const UPDATE = 'UPDATE';
    const MAGENTO_TIPS = 'MAGENTO_TIPS';
    public function toOptionArray()
    {
        $types = [
            [
                'value' => self::GENERAL,
                'label' => __('General Info')
            ],
            [
                'value' => self::SPECIAL_DEALS,
                'label' => __('Promotions')
            ],
            [
                'value' => self::UPDATE,
                'label' => __('Update extensions')
            ],
            [
                'value' => self::MAGENTO_TIPS,
                'label' => __('Magento Tips')
            ]
        ];
        return $types;
    }
}