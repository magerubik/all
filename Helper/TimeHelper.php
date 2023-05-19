<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
*/
namespace Magerubik\All\Helper;
use Magento\Framework\App\Helper\Context;
/**
 * Class TimeHelper
 * @package Magerubik\All\Helper
 */
class TimeHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MAGERUBIK_WORD = 'UGxlYXNlIGVudGVyIGxpY2Vuc2Uga2V5ICE=';
	/**
     * TimeHelper constructor.
     * @param Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $timezone
     */
    public function __construct(
        Context $context,
		\Magerubik\All\Model\ResourceModel\License\CollectionFactory $collectionFactory,
        \Magento\Framework\Stdlib\DateTime\Timezone $timezone
    )
    {
		parent::__construct($context);
        $this->timezone = $timezone;
		$this->collectionFactory = $collectionFactory;
    }
    public function getYourDomain($url)   {
        $domain = 'dev';
        $domain = $_SERVER['SERVER_NAME'];
        if ( !preg_match("/^http/", $url) ) $url = 'http://' . $url;
        if ( $url[strlen($url)-1] != '/' ) $url .= '/';
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if ( preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs) ) {
            $res = preg_replace('/^www\./', '', $regs['domain'] );
            return $res;
        }
        return $domain;
    }
	public function getCurrentTime ($format = '') {
        $intTime = $this->timezone->scopeTimeStamp();
        if($format != '')
            return date($format,$intTime);
        return $intTime;
    }
	public function getActiveModule ($moduleCode) {
        $mainDomain = $this->getYourDomain( $_SERVER['SERVER_NAME']);
        if($mainDomain != 'dev') {
            $isVaild = false;
            $collection = $this->collectionFactory->create()->addFieldToFilter('licence_path',$moduleCode);
            if($collection->getSize() > 0) {
				foreach ($collection as $item)
                {
                    if($item->getData('licence_code') == md5($mainDomain.$moduleCode.$item->getData('licence_key'))) {
                        $isVaild = true;
                        break;
                    }
                }
            }
        }
		return $isVaild;
    }
}