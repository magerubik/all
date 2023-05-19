<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
declare(strict_types=1);
namespace Magerubik\All\Model\Feed\FeedTypes;
use Magerubik\All\Model\Feed\FeedContentProvider;
use Magerubik\All\Model\Parser;
use Magerubik\All\Helper\Data as Serializer;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Escaper;
class Extensions
{
    const EXTENSIONS_CACHE_ID = 'mrall_extensions';
    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var FeedContentProvider
     */
    private $feedContentProvider;
    /**
     * @var Parser
     */
    private $parser;
    /**
     * @var Escaper
     */
    private $escaper;
    public function __construct(
        Serializer $serializer,
        CacheInterface $cache,
        FeedContentProvider $feedContentProvider,
        Parser $parser,
        Escaper $escaper
    ) {
        $this->serializer = $serializer;
        $this->cache = $cache;
        $this->feedContentProvider = $feedContentProvider;
        $this->parser = $parser;
        $this->escaper = $escaper;
    }
    /**
     * @return array
     */
    public function execute(): array
    {
		if ($cache = $this->cache->load(self::EXTENSIONS_CACHE_ID)) {
            return $this->serializer->unserialize($cache);
        }
        return $this->getFeed();
    }
    /**
     * @return array
     */
    public function getFeed(): array
    {
        $result = [];
        $content = $this->feedContentProvider->getFeedContent(
            $this->feedContentProvider->getFeedUrl(FeedContentProvider::URN_EXTENSIONS)
        );
        $feedXml = $this->parser->parseXml($content);
        if (isset($feedXml->channel->item)) {
            $result = $this->prepareFeedData($feedXml);
        }
		$this->cache->save(
            $this->serializer->serialize($result),
            self::EXTENSIONS_CACHE_ID,
            [self::EXTENSIONS_CACHE_ID]
        );
        return $result;
    }
    /**
     * @param \SimpleXMLElement $feedXml
     * @return array
     */
    private function prepareFeedData(\SimpleXMLElement $feedXml): array
    {
        $result = [];
        foreach ($feedXml->channel->item as $item) {
            $code = $this->escaper->escapeHtml((string)$item->code);
            if (!isset($result[$code])) {
                $result[$code] = [];
            }
            $title = $this->escaper->escapeHtml((string)$item->title);
            $productPageLink = $item->link;
            $dateString = !empty((string)$item->date) ? $this->convertDate((string)$item->date) : '';
            $result[$code][$title] = [
                'name' => $title,
                'url' => $this->escaper->escapeUrl((string)$productPageLink),
                'version' => $this->escaper->escapeHtml((string)$item->version),
                'guide' => $this->escaper->escapeUrl((string)$item->guide),
                'date' => $this->escaper->escapeHtml($dateString),
                'price' => $this->escaper->escapeHtml((int)$item->price)
            ];
        }
        return $result;
    }
    /**
     * @param string $date
     *
     * @return string
     * @throws \Exception
     */
    private function convertDate($date)
    {
        try {
            $dateTimeObject = new \DateTime($date);
        } catch (\Exception $e) {
            return '';
        }
        return $dateTimeObject->format('F j, Y');
    }
}