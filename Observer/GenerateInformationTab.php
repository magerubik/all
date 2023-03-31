<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Observer;
use Magerubik\All\Model\Feed\ExtensionsProvider;
use Magerubik\All\Model\ModuleInfoProvider;
use Magento\Config\Model\Config\Structure;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Asset\Repository;
class GenerateInformationTab implements ObserverInterface
{
    private $block;
    /**
     * @var string
     */
    private $moduleLink;
    /**
     * @var string
     */
    private $moduleCode;
    /**
     * @var Manager
     */
    private $moduleManager;
    /**
     * @var Repository
     */
    private $assetRepo;
    /**
     * @var Structure
     */
    private $configStructure;
    /**
     * @var ExtensionsProvider
     */
    private $extensionsProvider;
    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;
    public function __construct(
        Manager $moduleManager,
        Repository $assetRepo,
        Structure $configStructure,
        ExtensionsProvider $extensionsProvider,
        ModuleInfoProvider $moduleInfoProvider
    ) {
        $this->moduleManager = $moduleManager;
        $this->assetRepo = $assetRepo;
        $this->configStructure = $configStructure;
        $this->extensionsProvider = $extensionsProvider;
        $this->moduleInfoProvider = $moduleInfoProvider;
    }
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getBlock();
        if ($block) {
            $this->setBlock($block);
            $html = $this->generateHtml();
            $block->setContent($html);
        }
    }
    /**
     * @return string
     */
    private function generateHtml()
    {
        $html = '<div class="magerubik-info-block">'
            . $this->showVersionInfo()
            . $this->additionalContent()
            . $this->showModuleExistingConflicts()
            . $this->getButtonsContainer();
        $html .= '</div>';
        return $html;
    }
    /**
     * @return string
     */
    protected function getLogoHtml()
    {
        $src = $this->assetRepo->getUrl('Magerubik_All::images/magerubik_logo.svg');
        $html = '<img class="magerubik-logo" src="' . $src . '"/>';
        return $html;
    }
    /**
     * @return string
     */
    private function additionalContent()
    {
        $html = '';
        $content = $this->getBlock()->getAdditionalModuleContent();
        if ($content) {
            if (!is_array($content)) {
                $content = [
                    [
                        'type' => 'success',
                        'text' => $content
                    ]
                ];
            }
            foreach ($content as $message) {
                if (isset($message['type'], $message['text'])) {
                    $html .= '<div class="magerubik-additional-content"><span class="message ' . $message['type'] . '">'
                        . $message['text']
                        . '</span></div>';
                }
            }
        }
        return $html;
    }
    /**
     * @return string
     */
    private function showVersionInfo()
    {
        $html = '<div class="magerubik-module-version">';
        $currentVer = $this->getCurrentVersion();
        if ($currentVer) {
            $isVersionLast = $this->isLastVersion($currentVer);
            $class = $isVersionLast ? 'last-version' : '';
            $html .= '<div><span class="version-title">'
                . $this->getModuleName() . ' '
                . '<span class="module-version ' . $class . '">' . $currentVer . '</span>'
                . __(' by ')
                . '</span>'
                . $this->getLogoHtml()
                . '</div>';
            if (!$isVersionLast) {
                $html .=
                    '<div><span class="upgrade-error message message-warning">'
                    . __(
                        'Update is available and recommended. See the '
                        . '<a target="_blank" href="%1">Change Log</a>',
                        $this->getChangeLogLink()
                    )
                    . '</span></div>';
            }
        }
        $html .= '</div>';
        return $html;
    }
    /**
     * @return string|null
     */
    protected function getCurrentVersion()
    {
        $data = $this->moduleInfoProvider->getModuleInfo($this->getModuleCode());
        return isset($data['version']) ? $data['version'] : null;
    }
    /**
     * @return string
     */
    private function getModuleCode()
    {
        if (!$this->moduleCode) {
            $this->moduleCode = '';
            $class = get_class($this->getBlock());
            if ($class) {
                $class = explode('\\', $class);
                if (isset($class[0], $class[1])) {
                    $this->moduleCode = $class[0] . '_' . $class[1];
                }
            }
        }
        return $this->moduleCode;
    }
    /**
     * @return string
     */
    protected function getChangeLogLink()
    {
        return $this->getModuleLink();
    }
    /**
     * @return string
     */
    private function getUserGuideContainer()
    {
        $html = '<div class="magerubik-user-guide"><span class="message success">'
            . __(
                'Need help with the settings?'
                . '  Please  consult the <a target="_blank" href="%1">user guide</a>'
                . ' to configure the extension properly.',
                $this->getUserGuideLink()
            )
            . '</span></div>';
        return $html;
    }
    /**
     * @return string
     */
    private function getUserGuideLink()
    {
        $link = $this->getBlock()->getUserGuide();
        return $link;
    }
    /**
     * @param string $currentVer
     *
     * @return bool
     */
    protected function isLastVersion($currentVer)
    {
        $result = true;
        $module = $this->extensionsProvider->getFeedModuleData($this->getModuleCode());
        if ($module && isset($module['version']) && version_compare($module['version'], (string)$currentVer, '>')
        ) {
            $result = false;
        }
        return $result;
    }
    /**
     * @return string
     */
    protected function getModuleName()
    {
        $result = '';
        $configTabs = $this->configStructure->getTabs();
        if ($name = $this->findResourceName($configTabs)) {
            $result = $name;
        }
        if (!$result) {
            $module = $this->extensionsProvider->getFeedModuleData($this->getModuleCode());
            $result = __('Extension');
            if ($module && isset($module['name'])) {
                $result = $module['name'];
                $result = str_replace(' for Magento 2', '', $result);
            }
        }
        return $result;
    }
    /**
     * @param $config
     *
     * @return string
     */
    protected function findResourceName($config)
    {
        $result = '';
        $currentNode = null;
        foreach ($config as $node) {
            if ($node->getId() === 'magerubik') {
                $currentNode = $node;
                break;
            }
        }
        if ($currentNode) {
            foreach ($currentNode->getChildren() as $item) {
                $data = $item->getData('resource');
                if (isset($data['label'], $data['resource'])
                    && strpos($data['resource'], $this->getModuleCode() . '::') !== false
                ) {
                    $result = $data['label'];
                    break;
                }
            }
        }
        return $result;
    }
    /**
     * @return string
     */
    private function getModuleLink()
    {
        if (!$this->moduleLink) {
            $this->moduleLink = '';
            $module = $this->extensionsProvider->getFeedModuleData($this->getModuleCode());
            if ($module && isset($module['url'])) {
                $this->moduleLink = $module['url'];
            }
        }
        return $this->moduleLink;
    }
    /**
     * @return string
     */
    private function showModuleExistingConflicts()
    {
        $html = '';
        $messages = [];
        if (count($messages)) {
            $html = '<div class="magerubik-conflicts-title">'
                . __('Problems detected:')
                . '</div>';
            $html .= '<div class="magerubik-disable-extensions">';
            foreach ($messages as $message) {
                $html .= '<p class="message message-error">' . $message . '</p>';
            }
            $html .= '</div>';
        }
        return $html;
    }
    /**
     * @return mixed
     */
    public function getBlock()
    {
        return $this->block;
    }
    /**
     * @param mixed $block
     */
    public function setBlock($block)
    {
        $this->block = $block;
    }
}