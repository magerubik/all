<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
*/
namespace Magerubik\All\Model;
/**
 * Class BookLicense
 * @package Magerubik\All\Model
 */
class License extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magerubik\All\Model\ResourceModel\License');
    }
}