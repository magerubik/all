<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
*/
namespace Magerubik\All\Model\ResourceModel\License;


/**
 * Class Collection
 * @package Magerubik\All\Model\ResourceModel\License
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Magerubik\All\Model\License',
            'Magerubik\All\Model\ResourceModel\License'
        );
    }

    /**
     * @param array $arFilter
     * @param array $arSelect
     * @param int $pages
     * @param string $sortBy
     * @param string $orderBy
     * @param array $options
     * @return Collection
     */
    function getAllLicenseItems($arFilter = array(), $arSelect = array('licence_id'), $pages = 10, $sortBy = 'licence_id', $orderBy = 'DESC', $options = array())
    {
        $collection = $this;
        $collection->addFieldToSelect($arSelect);
        if(count($arFilter))
        {
            foreach ($arFilter as  $key => $value)
            {
                $collection->addFieldToFilter($key,$value);
            }
        }
        $collection->setPageSize($pages);
        $collection->setOrder($sortBy,$orderBy);
        return $collection;
    }
}