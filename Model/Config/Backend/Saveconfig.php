<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
*/
namespace Magerubik\All\Model\Config\Backend;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magerubik\All\Helper\Data;
use Magerubik\All\Helper\TimeHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Module\ModuleListInterface;
use Magerubik\All\Model\Feed\FeedContentProvider;
use Magerubik\All\Model\Parser;
use Magento\Framework\Escaper;
class Saveconfig extends \Magento\Framework\App\Config\Value
{
    protected $licenseFactory;
    protected $curlClient;
    protected $scopeconfig;
    protected $request;
    protected $configWriter;
    protected $resourceConnection;
    protected $timeHelper;
	protected $moduleList;
	protected $feedContentProvider;
	protected $parser;
	protected $escaper;
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magerubik\All\Model\LicenseFactory $licenseFactory,
		ModuleListInterface $moduleList,
		FeedContentProvider $feedContentProvider,
        TimeHelper $timeHelper,
        Curl $curl,
        RequestInterface $request,
        ResourceConnection $resourceConnection,
		Parser $parser,
		Escaper $escaper,
        array $data = [])
    {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->curlClient = $curl;
        $this->scopeconfig = $config;
        $this->request = $request;
        $this->resourceConnection = $resourceConnection;
        $this->licenseFactory = $licenseFactory;
        $this->timeHelper = $timeHelper;
		$this->moduleList = $moduleList;
		$this->feedContentProvider = $feedContentProvider;
		$this->parser = $parser;
		$this->escaper = $escaper;
    }
    public function beforeSave()
    {
        return parent::beforeSave();
    }
    public function afterSave()
    {
        $license_key = trim($this->getValue());
        $domain = $_SERVER['SERVER_NAME'];
        $mainDomain = $this->timeHelper->getYourDomain($domain);
        $moduleList = $this->getModuleList();
        $data = array(
            'license_key' => $license_key,
            'domain' => $domain,
            'main_domain'=>$mainDomain,
            'module_list'=>$moduleList
        );
        $url = "aHR0cHM6Ly9tYWdlcnViaWsuY29tL2xpY2Vuc2UvY2hlY2tsaWNlbnNlL2luZGV4";
        //$url = "aHR0cDovL2Z1bGxkZW1vLmNvbS9saWNlbnNlL2NoZWNrbGljZW5zZS9pbmRleA==";
        $datasend = urlencode(serialize($data));
        $getlink = base64_decode($url)."?str=".$datasend;
        $this->curlClient->get($getlink);
        $result = $this->curlClient->getBody();
        $result_data = json_decode($result, true);
        $current_time = $this->timeHelper->getCurrentTime('Y-m-d H:i:s');
		if(count($moduleList)>0){
			foreach ($moduleList as $module) {
				$license = $this->licenseFactory->create();
				$status = isset($result_data[$module]['status']) ? $result_data[$module]['status'] : '';
				$extension_code = isset($result_data[$module]['extension_code']) ? $result_data[$module]['extension_code'] : 'NO DATA';
				$domain_count = isset($result_data[$module]['domain_count']) ? $result_data[$module]['domain_count'] : '';
				if ($status == "true"){
					$licenseId = $this->getCurLicense($module);
					$license->setLicenceDomains($domain);
					$license->setLicenceCount($domain_count);
					$license->setLicenceCode($extension_code);
					$license->setLicenceKey($license_key);
					$license->setLicencePath($module);
					$license->setCreatedTime($current_time);
					$license->setIsValid(1);
					if($licenseId > 0) {
						$license->setId($licenseId);
					}
					$license->save();
				} else {
					$licenseId = $this->getCurLicense($module);
					if($licenseId > 0) {
						$license->setLicenceKey($license_key);
						$license->setLicenceCode('');
						$license->setIsValid(0);
						$license->setId($licenseId);
						$license->save();
					}
				}
			}
		}
        $this->_cacheManager->clean();
        return parent::afterSave();
    }
    function getCurLicense($module)
    {
        $collection = $this->licenseFactory->create()->getCollection()
            ->addFieldToFilter('licence_path', array('finset'=> $module));
        return $collection && $collection->getSize() > 0 ? $collection->getFirstItem()->getId() : 0;
    }
	public function getModuleList()
    {
        $modullist = [];
        $modules = $this->moduleList->getNames();
        $modulesFree = $this->getFreeFeed();
        foreach ($modules as $moduleName) {
            if ($moduleName === 'Magerubik_All' || strpos($moduleName, 'Magerubik_') === false || in_array($moduleName, $modulesFree))
			{
                continue;
            }
			$modullist[] = $moduleName;
        }
        return $modullist;
    }
	public function getFreeFeed(): array
    {
        $result = [];
        $content = $this->feedContentProvider->getFeedContent(
            $this->feedContentProvider->getFeedUrl(FeedContentProvider::URN_FREE_EXTENSIONS)
        );
        $feedXml = $this->parser->parseXml($content);
        if (isset($feedXml->channel->item)) {
		   $result = $this->prepareFeedData($feedXml);
        }
        return $result;
    }
	private function prepareFeedData(\SimpleXMLElement $feedXml): array
    {
        $result = [];
        foreach ($feedXml->channel->item as $item) {
			$code = $this->escaper->escapeHtml((string)$item->code);
            $result[] = $code;
        }
        return $result;
    }
}