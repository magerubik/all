<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * Copyright © 2021 magerubik.com. All rights reserved. <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Block\Adminhtml\Button\Edit;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
class Delete extends Generic implements ButtonProviderInterface
{
    protected $context;
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }
    public function getButtonData()
    {
        $data = [];
        $id = $this->context->getRequest()->getParam('id');
        if ($id) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }
    public function getDeleteUrl()
    {
        $id = $this->context->getRequest()->getParam('id');
        return $this->getUrl('*/*/delete', ['id' => $id]);
    }
}