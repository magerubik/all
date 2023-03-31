<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * Copyright © 2021 magerubik.com. All rights reserved. <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Model;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Uploader;
class ImageProcessor
{
    /**
     * @var \Magento\Catalog\Model\ImageUploader
     */
    private $imageUploader;
    /**
     * @var \Magento\Framework\ImageFactory
     */
    private $imageFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;
    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    private $coreFileStorageDatabase;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magerubik\All\Model\ImageUploader $imageUploader,
        \Magento\Framework\ImageFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->imageUploader = $imageUploader;
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        $this->ioFile = $ioFile;
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->logger = $logger;
    }
    /**
     * @return \Magento\Framework\Filesystem\Directory\WriteInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }
    /**
     * @param $imageName
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getThumbnailUrl($imageName, $imagePath=null)
    {
		if($imagePath==null) $imagePath = $this->imageUploader->getBasePath(). '/';
		$pubDirectory = $this->filesystem->getDirectoryRead(DirectoryList::PUB);
        if ($pubDirectory->isExist($imageName)) {
            $result = $this->storeManager->getStore()->getBaseUrl() . trim($imageName, '/');
        } else {
			$result = $this->getCategoryIconMedia($imagePath) . $imageName;
        }
        return $result;
    }
    /**
     * @param string $iconName
     *
     * @return string
     */
    private function getImageRelativePath($iconName)
    {
        return $this->imageUploader->getBasePath() . DIRECTORY_SEPARATOR . $iconName;
    }
    /**
     * @param $mediaPath
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCategoryIconMedia($mediaPath)
    {
        return $this->storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $mediaPath;
    }
    /**
     * @param $iconName
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processCategoryIcon($iconName)
    {
        $this->imageUploader->moveFileFromTmp($iconName, true);
        $filename = $this->getMediaDirectory()->getAbsolutePath($this->getImageRelativePath($iconName));
        try {
            /** @var \Magento\Framework\Image $imageProcessor */
            $imageProcessor = $this->imageFactory->create(['fileName' => $filename]);
            $imageProcessor->keepAspectRatio(true);
            $imageProcessor->keepFrame(true);
            $imageProcessor->keepTransparency(true);
            $imageProcessor->backgroundColor([255, 255, 255]);
            $imageProcessor->save();
        } catch (\Exception $e) {
            null;// Unsupported image format.
        }
    }
    public function moveFile(array $images): ?string
    {
        $filePath = null;
        if (count($images) > 0) {
            foreach ($images as $image) {
                if (array_key_exists('file', $image)) {
                    $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                    if ($mediaDirectory->isExist($this->imageUploader->getBaseTmpPath() . '/' . $image['file'])) {
                        $filePath = $this->moveFileFromTmp($image['file']);
                        break;
                    }
                } elseif (isset($image['type'])) {
                    $filePath = $image['url'] ?? '';
                }
            }
        }
        return $filePath;
    }
	public function MrMoveFile($fileName, $imgPath)
    {
		$filePath = null;
		$mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
		if ($mediaDirectory->isExist($imgPath . 'tmp/' . $fileName)) {
			$filePath = $this->moveFileFromTmp($fileName, $imgPath . 'tmp', $imgPath);
		}
		return $filePath;
    }
    /**
     * @param $iconName
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function deleteImage($iconName)
    {
        $this->getMediaDirectory()->delete($this->getImageRelativePath($iconName));
    }
    /**
     * @param $imageName
     *
     * @return array|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function copy($imageName)
    {
        $basePath = $this->getMediaDirectory()->getAbsolutePath($this->getImageRelativePath($imageName));
        $imageName = explode('.', $imageName);
        $imageName[0] .= '-' . random_int(1, 1000);
        $imageName = implode('.', $imageName);
        $newPath = $this->getMediaDirectory()->getAbsolutePath($this->getImageRelativePath($imageName));
        try {
            $this->ioFile->cp(
                $basePath,
                $newPath
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }
        return $imageName;
    }
    /**
     * for fix SaveBaseCategoryImageInformation Plugin on ce
     */
    public function moveFileFromTmp($imageName, $baseTmpPath = null, $basePath = null, $returnRelativePath = false): string
    {
        if($baseTmpPath == null){
			$baseTmpPath = $this->imageUploader->getBaseTmpPath();
			$basePath = $this->imageUploader->getBasePath();
		}
        $baseImagePath = $this->imageUploader->getFilePath(
            $basePath,
            Uploader::getNewFileName(
                $this->getMediaDirectory()->getAbsolutePath($this->imageUploader->getFilePath($basePath, $imageName))
            )
        );
        $baseTmpImagePath = $this->imageUploader->getFilePath($baseTmpPath, $imageName);
        try {
            $this->coreFileStorageDatabase->copyFile($baseTmpImagePath, $baseImagePath);
            $this->getMediaDirectory()->renameFile($baseTmpImagePath, $baseImagePath);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while saving the file(s).'),
                $e
            );
        }
        return $returnRelativePath ? $baseImagePath : $imageName;
    }
}