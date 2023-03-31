<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
declare(strict_types=1);
namespace Magerubik\All\Model\Feed\FeedTypes;
use Magerubik\All\Model\AdminNotification\Model\ResourceModel\Inbox\Collection\ExistsFactory;
use Magerubik\All\Model\Config;
use Magerubik\All\Model\Feed\FeedContentProvider;
use Magerubik\All\Model\ModuleInfoProvider;
use Magerubik\All\Model\Parser;
use Magerubik\All\Model\Source\NotificationType;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Notification\MessageInterface;
class News
{
    protected $magerubikModules = [];
    /**
     * @var Config
     */
    private $config;
    /**
     * @var FeedContentProvider
     */
    private $feedContentProvider;
    /**
     * @var Parser
     */
    private $parser;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;
    /**
     * @var ModuleListInterface
     */
    private $moduleList;
    /**
     * @var ExistsFactory
     */
    private $inboxExistsFactory;
    /**
     * @var Escaper
     */
    private $escaper;
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;
    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;
    public function __construct(
        Config $config,
        FeedContentProvider $feedContentProvider,
        Parser $parser,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
        ExistsFactory $inboxExistsFactory,
        Escaper $escaper,
        DataObjectFactory $dataObjectFactory,
        ModuleInfoProvider $moduleInfoProvider
    ) {
        $this->config = $config;
        $this->feedContentProvider = $feedContentProvider;
        $this->parser = $parser;
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleList;
        $this->inboxExistsFactory = $inboxExistsFactory;
        $this->escaper = $escaper;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->moduleInfoProvider = $moduleInfoProvider;
    }
    /**
     * @return array
     */
    public function execute(): array
    {
        $feedData = [];
        $allowedNotifications = $this->config->getEnabledNotificationTypes();
        if (empty($allowedNotifications)) {
            return $feedData;
        }
        $maxPriority = 0;
        $content = $this->feedContentProvider->getFeedContent(
            $this->feedContentProvider->getFeedUrl(FeedContentProvider::URN_NEWS)
        );
        $feedXml = $this->parser->parseXml($content);
        if (isset($feedXml->channel->item)) {
            $installDate = $this->config->getFirstModuleRun();
            foreach ($feedXml->channel->item as $item) {
                if (!array_intersect($this->convertToArray($item->type ?? ''), $allowedNotifications))
				{
                    continue;
                }
                $priority = $item->priority ?? 1;
                if ($priority <= $maxPriority || !$this->isItemValid($item)) {
                    continue;
                }
                $date = strtotime((string)$item->pubDate);
                $expired = isset($item->expirationDate) ? strtotime((string)$item->expirationDate) : null;
                
				if ($installDate <= $date && (!$expired || $expired > gmdate('U'))) {
                    $maxPriority = $priority;
                    $expired = $expired ? date('Y-m-d H:i:s', $expired) : null;
                    $feedData = [
                        'severity'        => MessageInterface::SEVERITY_NOTICE,
                        'date_added'      => date('Y-m-d H:i:s', $date),
                        'expiration_date' => $expired,
                        'title'           => $this->convertString($item->title),
                        'description'     => $this->convertString($item->description),
                        'url'             => $this->convertString($item->link),
                        'is_magerubik'       => 1,
                        'image_url'       => $this->convertString($item->image)
                    ];
                }
            }
        }
        return $feedData;
    }
    /**
     * @param \SimpleXMLElement $item
     *
     * @return bool
     */
    protected function isItemValid(\SimpleXMLElement $item): bool
    {
        return !$this->isItemExists($item);
    }
    /**
     * @param mixed $value
     *
     * @return array
     */
    private function convertToArray($value): array
    {
        return explode(',', (string)$value);
    }
    /**
     * @param \SimpleXMLElement $data
     *
     * @return string
     */
    private function convertString(\SimpleXMLElement $data): string
    {
        return $this->escaper->escapeHtml((string)$data);
    }
    /**
     * @param \SimpleXMLElement $item
     *
     * @return bool
     */
    private function isItemExists(\SimpleXMLElement $item): bool
    {
        return $this->inboxExistsFactory->create()->execute($item);
    }
}