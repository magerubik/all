<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
namespace Magerubik\All\Model\Config\Backend;
use Magerubik\All\Model\Source\NotificationType;
class Unsubscribe extends \Magento\Framework\App\Config\Value implements
    \Magento\Framework\App\Config\Data\ProcessorInterface
{
    const PATH_TO_FEED_IMAGES = 'https://media.magerubik.com/notification';
    /**
     * @var \Magerubik\All\Model\AdminNotification\Messages
     */
    private $messageManager;
    /**
     * @var NotificationType
     */
    private $notificationType;
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magerubik\All\Model\AdminNotification\Messages $messageManager,
        NotificationType $notificationType,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->messageManager = $messageManager;
        $this->notificationType = $notificationType;
    }
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->prepareMessage();
        }
        return parent::afterSave();
    }
    private function prepareMessage()
    {
        $value = explode(',', $this->getValue());
        if (empty($this->getValue())) {
            $changes = ['empty'];
        } else {
            $oldValue = explode(',', $this->getOldValue());
            $changes = array_diff($oldValue, $value);
        }
        if (!empty($changes)) {
            foreach ($changes as $change) {
                $message = $this->generateMessage($change);
                $this->messageManager->addMessage($message);
            }
        } else {
            $this->messageManager->clear();
        }
    }
    /**
     * Process config value
     *
     * @param string $value
     * @return string
     */
    public function processValue($value)
    {
        return $value;
    }
    protected function generateMessage($change)
    {
        $message = '';
        $titles = $this->notificationType->toOptionArray();
        if ($change === 'empty') {
			$label = __('All Notifications');
			$message = '<img src="' . $this->generateLink($change) .'"/><span>'
						. __('You have successfully unsubscribed from %1.', $label) .'</span>';
        } else {
			foreach ($titles as $title) {
				if ($title['value'] === $change) {
					$label = $title['label'];
					$message = '<img src="' . $this->generateLink($change) .'"/><span>'
						. __('You have successfully unsubscribed from %1.', $label) .'</span>';
					break;
				}
			}
        }
        return $message;
    }
    private function generateLink($change)
    {
        $change = mb_strtolower($change);
        return self::PATH_TO_FEED_IMAGES . $change . '.png';
    }
}