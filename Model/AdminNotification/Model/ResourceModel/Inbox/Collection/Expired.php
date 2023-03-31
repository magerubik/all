<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */


namespace Magerubik\All\Model\AdminNotification\Model\ResourceModel\Inbox\Collection;

class Expired extends \Magento\AdminNotification\Model\ResourceModel\Inbox\Collection
{
    /**
     * @return \Magento\AdminNotification\Model\ResourceModel\Inbox\Collection\Unread
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('is_remove', 0)
            ->addFieldToFilter('is_magerubik', 1)
            ->addFieldToFilter('expiration_date', ['notnull' => true]);
        $this->getSelect()->where('expiration_date < NOW()');

        return $this;
    }
}
