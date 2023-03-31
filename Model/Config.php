<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Model;
use Magerubik\All\Model\Source\NotificationType;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\ScopeInterface;
/**
 * Class Config provide configuration data
 */
class Config extends ConfigProviderAbstract
{
    /**
     * xpath prefix of module (section)
     */
    protected $pathPrefix = 'magerubik_all/';
    /**#@+
     * xpath group parts
     */
    const NOTIFICATIONS_BLOCK = 'notifications/';
    const SYSTEM_VALUE_BLOCK = 'system_value/';
    /**#@-*/
    /**#@+
     * xpath field parts
     */
    const LAST_UPDATE = 'last_update';
    const FREQUENCY = 'frequency';
    const FIRST_MODULE_RUN = 'first_module_run';
    const REMOVE_DATE = 'remove_date';
    const NOTIFICATIONS_TYPE = 'type';
    /**#@-*/
    const HOUR_MIN_SEC_VALUE = 60 * 60 * 24;
    const REMOVE_EXPIRED_FREQUENCY = 60 * 60 * 6;
    /**
     * @var WriterInterface
     */
    private $configWriter;
    /**
     * @var ReinitableConfigInterface
     */
    private $reinitableConfig;
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        ReinitableConfigInterface $reinitableConfig
    ) {
        parent::__construct($scopeConfig);
        $this->configWriter = $configWriter;
        $this->reinitableConfig = $reinitableConfig;
    }
    /**
     * @return int
     */
    public function getFrequencyInSec()
    {
        return $this->getCurrentFrequencyValue() * self::HOUR_MIN_SEC_VALUE;
    }
    /**
     * @return int
     */
    public function getLastUpdate()
    {
        return $this->getValue(self::SYSTEM_VALUE_BLOCK . self::LAST_UPDATE);
    }
    /**
     * @return int
     */
    public function getLastRemovement()
    {
        return $this->getValue(self::SYSTEM_VALUE_BLOCK . self::REMOVE_DATE);
    }
    /**
     * Save Last Update
     */
    public function setLastUpdate()
    {
        $this->configWriter->save($this->pathPrefix . self::SYSTEM_VALUE_BLOCK . self::LAST_UPDATE, time());
        $this->reinitableConfig->reinit();
        $this->clean();
    }
    /**
     * @return int
     */
    public function getFirstModuleRun()
    {
        $result = $this->getValue(self::SYSTEM_VALUE_BLOCK . self::FIRST_MODULE_RUN);
        if (!$result) {
            $result = time();
            $this->configWriter->save($this->pathPrefix . self::SYSTEM_VALUE_BLOCK . self::FIRST_MODULE_RUN, $result);
            $this->reinitableConfig->reinit();
            $this->clean();
        }
        return $result;
    }
    /**
     * Save Last Removement
     */
    public function setLastRemovement()
    {
        $this->configWriter->save($this->pathPrefix . self::SYSTEM_VALUE_BLOCK . self::REMOVE_DATE, time());
        $this->reinitableConfig->reinit();
        $this->clean();
    }
    /**
     * @return int
     */
    public function getCurrentFrequencyValue()
    {
        return $this->getValue(self::NOTIFICATIONS_BLOCK . self::FREQUENCY);
    }
    /**
     * @param int $value
     */
    public function changeFrequency($value)
    {
        $this->configWriter->save($this->pathPrefix . self::NOTIFICATIONS_BLOCK . self::FREQUENCY, $value);
        $this->reinitableConfig->reinit();
        $this->clean();
    }
    /**
     * @return array
     */
    public function getEnabledNotificationTypes()
    {
        $value = $this->getValue(self::NOTIFICATIONS_BLOCK . self::NOTIFICATIONS_TYPE);
        return empty($value) ? [] : explode(',', $value);
    }
}