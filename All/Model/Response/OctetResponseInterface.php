<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Model\Response;

use Magento\Framework\App;
use Magento\Framework\Filesystem\File\ReadInterface;

interface OctetResponseInterface extends App\Response\HttpInterface, App\PageCache\NotCacheableInterface
{
    const FILE = 'file';
    const FILE_URL = 'url';
    public function sendOctetResponse();
    public function getContentDisposition(): string;
    public function getReadResourceByPath(string $readResourcePath): ReadInterface;
    public function setReadResource(ReadInterface $readResource): OctetResponseInterface;
    public function getFileName(): ?string;
    public function setFileName(string $fileName): OctetResponseInterface;
}
