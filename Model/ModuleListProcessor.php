<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Model;
use Magerubik\All\Model\Feed\ExtensionsProvider;
use Magento\Framework\Module\ModuleListInterface;
use Magerubik\All\Model\ResourceModel\License\CollectionFactory;
use Magerubik\All\Helper\TimeHelper;
class ModuleListProcessor
{
    /**
     * @var ModuleListInterface
     */
    private $moduleList;
    /**
     * @var array
     */
    private $modules;
    /**
     * @var Feed\ExtensionsProvider
     */
    private $extensionsProvider;
    /**
     * @var ModuleInfoProvider
     */
	private $timeHelper;
    private $moduleInfoProvider;
    private $collectionFactory;
    public function __construct(
        ModuleListInterface $moduleList,
        ExtensionsProvider $extensionsProvider,
		TimeHelper $timeHelper,
		CollectionFactory $collectionFactory,
        ModuleInfoProvider $moduleInfoProvider
    ) {
        $this->moduleList = $moduleList;
        $this->extensionsProvider = $extensionsProvider;
        $this->moduleInfoProvider = $moduleInfoProvider;
		$this->timeHelper = $timeHelper;
		$this->collectionFactory = $collectionFactory;
    }
    /**
     * @return array
     */
    public function getModuleList()
    {
        if ($this->modules !== null) {
            return $this->modules;
        }
        $this->modules = [
            'lastVersion' => [],
            'hasUpdate' => []
        ];
        $modules = $this->moduleList->getNames();
        sort($modules);
        foreach ($modules as $moduleName) {
            if ($moduleName === 'Magerubik_All' || strpos($moduleName, 'Magerubik_') === false)
			{
                continue;
            }
            try {
                if (!is_array($module = $this->getModuleInfo($moduleName))) {
                    continue;
                }
            } catch (\Exception $e) {
                continue;
            }
            if (empty($module['hasUpdate'])) {
                $this->modules['lastVersion'][] = $module;
            } else {
                $this->modules['hasUpdate'][] = $module;
            }
        }
        return $this->modules;
    }
    /**
     * @param string $moduleCode
     * @return array|mixed|string
     */
    protected function getModuleInfo($moduleCode)
    {
        $module = $this->moduleInfoProvider->getModuleInfo($moduleCode);
		
		if (!is_array($module) || !isset($module['version']) || !isset($module['description']))
		{
			return '';
        }
        $currentVer = $module['version'];
        $allExtensions = $this->extensionsProvider->getAllFeedExtensions();
        if ($allExtensions && isset($allExtensions[$moduleCode])) {
			$ext = end($allExtensions[$moduleCode]);
            $lastVer = $ext['version'];
            $module['lastVersion'] = $lastVer;
            $module['hasUpdate'] = version_compare($currentVer, $lastVer, '<');
            $module['description'] = $ext['name'];
            $module['url'] = !empty($ext['url']) ? $ext['url'] : '';
            $module['date'] = !empty($ext['date']) ? $ext['date'] : '';
			$price = !empty($ext['price']) ? $ext['price'] : 0;
			if($price==0){
				$module['active']  = '<p><b style="color: #00a824;">FREE</b></p>';
			} else {
				$module['active']  = $this->_getActiveHtml($moduleCode);
			}
			
            return $module;
        }
        return '';
    }
	protected function _getActiveHtml($moduleCode)
    {
		$html = base64_decode('PHAgc3R5bGU9ImNvbG9yOiByZWQ7Ij48Yj5OT1QgVkFMSUQ8L2I+PC9wPg==');
        $mainDomain = $this->timeHelper->getYourDomain( $_SERVER['SERVER_NAME']);
        if($mainDomain != 'dev') {
            $isVaild = false;
            $collection = $this->collectionFactory->create()->addFieldToFilter('licence_path',$moduleCode);
            $domainCount = 0;
            $timeActive = '';
            if($collection->getSize() > 0) {
				foreach ($collection as $item)
                {
                    if($item->getData('licence_code') == md5($mainDomain.$moduleCode.$item->getData('licence_key'))) {
                        $isVaild = true;
                        $domainCount = $item->getData('licence_count');
                        $timeActive = $item->getData('created_time');
                        break;
                    }
                }
            }
            if($isVaild) {
                $html = base64_decode('PGI+W0RvbWFpbkNvdW50XSBEb21haW4gTGljZW5zZTwvYj48YnI+PHAgc3R5bGU9IndoaXRlLXNwYWNlOiBub3dyYXA7Ij48Yj5BY3RpdmUgRGF0ZTogPC9iPltDcmVhdGVkVGltZV08L3A+');
                $html = str_replace(array('[DomainCount]','[CreatedTime]'),array($domainCount,$timeActive),$html);
            }
        }
		return $html;
	}
}