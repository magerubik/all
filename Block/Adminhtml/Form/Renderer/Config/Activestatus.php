<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
*/
namespace Magerubik\All\Block\Adminhtml\Form\Renderer\Config;
class Activestatus extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $timeHelper;
    protected $collectionFactory;
    function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magerubik\All\Helper\TimeHelper $timeHelper,
        \Magerubik\All\Model\ResourceModel\License\CollectionFactory $collectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->timeHelper = $timeHelper;
        $this->collectionFactory = $collectionFactory;
    }
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = base64_decode('PHAgc3R5bGU9ImNvbG9yOiByZWQ7Ij48Yj5OT1QgVkFMSUQ8L2I+PC9wPjxhIGhyZWY9Imh0dHBzOi8vbWFnZXJ1YmlrLmNvbS9jb250YWN0IiB0YXJnZXQ9Il9ibGFuayI+Q29udGFjdDwvYT48L2JyPg==');
        $mainDomain = $this->timeHelper->getYourDomain( $_SERVER['SERVER_NAME']);
        if($mainDomain != 'dev') {
            $isVaild = false;
            $collection = $this->collectionFactory->create()->addFieldToFilter('licence_path','magerubik_all/license/key');
            $domainCount = 0;
            $timeActive = '';
            if($collection->getSize() > 0) {
                foreach ($collection as $item)
                {
                    if($item->getData('licence_code') == md5($mainDomain.$item->getData('licence_key'))) {
                        $isVaild = true;
                        $domainCount = $item->getData('licence_count');
                        $timeActive = $item->getData('created_time');
                        break;
                    }
                }
            }
            if($isVaild) {
                $html = base64_decode('PGhyIHdpZHRoPSIyODAiPjxiPltEb21haW5Db3VudF0gRG9tYWluIExpY2Vuc2U8L2I+PGJyPjxiPkFjdGl2ZSBEYXRlOiA8L2I+W0NyZWF0ZWRUaW1lXTxicj48YSBocmVmPSJodHRwczovL21hZ2VydWJpay5jb20vY29udGFjdCIgdGFyZ2V0PSJfYmxhbmsiPkNvbnRhY3Q8L2E+PGJyPg==');
                $html = str_replace(array('[DomainCount]','[CreatedTime]'),array($domainCount,$timeActive),$html);
            }
        }
        return $html;
    }
}