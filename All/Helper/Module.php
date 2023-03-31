<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Helper;
use Magerubik\All\Model\Feed\ExtensionsProvider;
use Magerubik\All\Model\ModuleInfoProvider;
/**
 * @deprecated Class for backward compatibility. Will be removed someday
 * @see ExtensionsProvider, ModuleInfoProvider
 */
class Module
{
    /**
     * @var ExtensionsProvider
     */
    private $extensionsProvider;
    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;
    public function __construct(
        ExtensionsProvider $extensionsProvider,
        ModuleInfoProvider $moduleInfoProvider
    ) {
        $this->extensionsProvider = $extensionsProvider;
        $this->moduleInfoProvider = $moduleInfoProvider;
    }
    /**
     * @deprecated since 1.10.2
     * @see \Magerubik\All\Model\Feed\ExtensionsProvider::getAllFeedExtensions
     */
    public function getAllExtensions()
    {
        return $this->extensionsProvider->getAllFeedExtensions();
    }
    /**
     * @deprecated since 1.10.2
     * @see \Magerubik\All\Model\Feed\ExtensionsProvider::getFeedModuleData()
     */
    public function getFeedModuleData($moduleCode)
    {
        return $this->extensionsProvider->getFeedModuleData($moduleCode);
    }
    /**
     * @deprecated since 1.10.2
     * @see \Magerubik\All\Model\ModuleInfoProvider::getModuleInfo
     */
    public function getModuleInfo($moduleCode)
    {
        return $this->moduleInfoProvider->getModuleInfo($moduleCode);
    }
}
