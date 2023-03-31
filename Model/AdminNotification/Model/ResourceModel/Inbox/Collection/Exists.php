<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */


namespace Magerubik\All\Model\AdminNotification\Model\ResourceModel\Inbox\Collection;

class Exists extends \Magento\AdminNotification\Model\ResourceModel\Inbox\Collection
{
    /**
     * @param \SimpleXMLElement $item
     * @return bool
     */
    public function execute(\SimpleXMLElement $item)
    {
        $this->addFieldToFilter('url', (string)$item->link);

        return $this->getSize() > 0;
    }
}
