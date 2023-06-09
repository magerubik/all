<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Model\Feed;
use Magento\Framework\HTTP\Adapter\Curl;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Store\Model\StoreManagerInterface;
/**
 * Class FeedContentProvider for reading file content by url
 */
class FeedContentProvider
{
    /**
     * Path to NEWS
     */
    const URN_NEWS = 'magerubik.com/feed-notification.xml';
    /**
     * Path to EXTENSIONS
     */
    const URN_EXTENSIONS = 'magerubik.com/feed-extensions.xml';
	/**
     * Path to FREE EXTENSIONS
     */
    const URN_FREE_EXTENSIONS = 'magerubik.com/feed-free-extensions.xml';
    /**
     * @var CurlFactory
     */
    private $curlFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Zend\Uri\Uri
     */
    private $baseUrlObject;
    public function __construct(
        CurlFactory $curlFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->curlFactory = $curlFactory;
        $this->storeManager = $storeManager;
    }
    /**
     * @param string $url
     *
     * @return false|string
     */
    public function getFeedContent($url)
    {
        /** @var Curl $curlObject */
        $curlObject = $this->curlFactory->create();
        $curlObject->setConfig(
            [
                'timeout' => 2,
                'useragent' => 'Magerubik All Feed'
            ]
        );
        $curlObject->write(\Zend_Http_Client::GET, $url, '1.0');
        $result = $curlObject->read();
        if ($result === false || $result === '') {
            return false;
        }
        $result = preg_split('/^\r?$/m', $result, 2);
        preg_match("/(?i)(\W|^)(Status: 404 File not found)(\W|$)/", $result[0], $notFoundFile);
        if ($notFoundFile) {
            return false;
        }
        $result = trim($result[1]);
        $curlObject->close();
        return $result;
    }
    /**
     * @param string $urn
     * @param bool $needFollowLocation
     *
     * @return string
     */
    public function getFeedUrl($urn, $needFollowLocation = true)
    {
        if ($needFollowLocation) {
            return 'https://' . $urn;
        }
        $scheme = $this->getCurrentScheme();
        $protocol = $scheme ?: 'http://';
        return $protocol . $urn;
    }
    /**
     * @return string
     */
    public function getCurrentScheme()
    {
        $scheme = $this->getBaseUrlObject()->getScheme();
        if ($scheme) {
            return $scheme . '://';
        }
        return '';
    }
    /**
     * @return string
     */
    public function getDomainZone()
    {
        $host = $this->getBaseUrlObject()->getHost();
        $host = explode('.', $host);
        return end($host);
    }
    /**
     * @return \Zend\Uri\Uri
     */
    private function getBaseUrlObject()
    {
        if ($this->baseUrlObject === null) {
            $url = $this->storeManager->getStore()->getBaseUrl();
            $this->baseUrlObject = \Zend\Uri\UriFactory::factory($url);
        }
        return $this->baseUrlObject;
    }
}