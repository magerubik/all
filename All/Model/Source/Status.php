<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * Copyright © 2021 magerubik.com. All rights reserved. <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Model\Source;
use Magento\Framework\Option\ArrayInterface;
/**
 * Class
 */
class Status implements ArrayInterface
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_DISABLED, 'label' => __('Disabled')],
            ['value' => self::STATUS_ENABLED, 'label' => __('Enabled')]
        ];
    }
}