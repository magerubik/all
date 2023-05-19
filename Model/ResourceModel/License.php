<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
*/
namespace Magerubik\All\Model\ResourceModel;
/**
 * Class License
 * @package Magerubik\All\Model\ResourceModel
 */
class License extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magerubik_licence', 'licence_id');
    }
}