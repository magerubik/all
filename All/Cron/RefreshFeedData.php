<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
declare(strict_types=1);
namespace Magerubik\All\Cron;
use Magerubik\All\Model\Feed\FeedTypes\Extensions;
class RefreshFeedData
{
    /**
     * @var Extensions
     */
    private $extensionsFeed;
    public function __construct(
        Extensions $extensionsFeed
    ) {
        $this->extensionsFeed = $extensionsFeed;
    }
    /**
     * Force reload feeds data
     */
    public function execute()
    {
        $this->extensionsFeed->getFeed();
    }
}