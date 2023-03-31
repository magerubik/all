<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * Copyright © 2021 magerubik.com. All rights reserved. <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;
class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MAGERUBIK_PATH = 'magerubik/';
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $imageFactory;
    public function __construct(
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->ioFile = $ioFile;
        $this->storeManager = $storeManager;
        $this->imageFactory = $imageFactory;
    }
    /**
     * @param $name
     * @param null $path
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl($name, $modulePath = null)
    {
        if (!$name) {
            return $name;
        }
		$moduleAbsolutePath = self::MAGERUBIK_PATH;
		if($modulePath != null){
			$moduleAbsolutePath .= $modulePath;
		}
        $fullPath = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            $moduleAbsolutePath
        );
        $result = '';
        if ($this->ioFile->fileExists($fullPath . $name) && $name != "") {
            $path = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $result = $path . $moduleAbsolutePath . $name;
        }
        return $result;
    }
    /**
     * @param $src
     * @param int $width
     * @param int $height
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getResizedImageUrl(
        $src,
        $width = 200,
        $height = 200,
		$modulePath = null
    ) {
        $dir = self::MAGERUBIK_PATH;
		if($modulePath != null){
			$dir .= $modulePath;
		}
        $absPath = $this->getAbsolutePath($dir);
        $absoluteImagePath = $absPath . $src;
        if (!$absPath || !$this->ioFile->fileExists($absoluteImagePath)) {
            return '';
        }
        $cachedImagePath = $this->getNewDirectoryImage($src, $width, $height);
        $resizedImagePath = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath($dir) . $cachedImagePath;
        $baseMediaUrl = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if (!$this->ioFile->fileExists($resizedImagePath)) {
            $this->resizeImage($width, $height, $absoluteImagePath, $resizedImagePath);
        }
        return $baseMediaUrl . $dir . $cachedImagePath;
    }
    /**
     * @param $dir
     *
     * @return string
     */
    private function getAbsolutePath($dir)
    {
        $path = '';
        if ($dir) {
            $path = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath($dir);
        }
        return  $path;
    }
    /**
     * @param $src
     * @param $width
     * @param $height
     * @return string
     */
    public function getNewDirectoryImage($src, $width, $height)
    {
        $segments = array_reverse(explode('/', $src));
        $first_dir = substr($segments[0], 0, 1);
        $second_dir = substr($segments[0], 1, 1);
        return 'cache/' . $first_dir . '/' . $second_dir . '/' . $width . '/' . $height . '/' . $segments[0];
    }
    /**
     * @param int $width
     * @param int $height
     * @param string $sourceImage
     * @param string $destinationPath
     *
     * @throws \Exception
     */
    private function resizeImage(int $width, int $height, string $sourceImage, string $destinationPath)
    {
        $imageResize = $this->imageFactory->create();
        $imageResize->open($sourceImage);
        $imageResize->backgroundColor([255, 255, 255]);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame(true);
        $imageResize->keepAspectRatio(true);
        $imageResize->resize($width, $height);
        $imageResize->save($destinationPath);
    }
}