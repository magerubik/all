<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * Copyright © 2021 magerubik.com. All rights reserved. <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Block\Adminhtml\Button\Edit;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
/**
 * Class
 */
class SaveAndContinue implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue'),
            'class' => 'save',
            'on_click' => '',
            'sort_order' => 90,
        ];
    }
}