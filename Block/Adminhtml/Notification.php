<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */


namespace Magerubik\All\Block\Adminhtml;

use Magerubik\All\Model\ModuleListProcessor;
use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Store\Model\ScopeInterface;
use Magerubik\All\Model\Source\NotificationType;
use Magerubik\All\Model\Config;

class Notification extends Field
{
    protected $_template = 'Magerubik_All::notification.phtml';

    /**
     * @var ModuleListProcessor
     */
    private $moduleListProcessor;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Template\Context $context,
        ModuleListProcessor $moduleListProcessor,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleListProcessor = $moduleListProcessor;
        $this->config = $config;
    }

    protected function _toHtml()
    {
        if ($this->isSetNotification()) {
			return parent::_toHtml();
        }

        return '';
    }

    /**
     * @return int|null
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getUpdatesCount()
    {
        $modules = $this->moduleListProcessor->getModuleList();

        return count($modules['hasUpdate']);
    }

    /**
     * @return bool
     */
    protected function isSetNotification()
    {
        $allowedNotifications = $this->config->getEnabledNotificationTypes();

        return empty($allowedNotifications);
    }
}
