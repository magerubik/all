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
use Magento\Framework\Data\Form\Element\AbstractElement;
class Extensions extends Field
{
    protected $_template = 'Magerubik_All::modules.phtml';
    /**
     * @var ModuleListProcessor
     */
    private $moduleListProcessor;
    public function __construct(
        Template\Context $context,
        ModuleListProcessor $moduleListProcessor,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleListProcessor = $moduleListProcessor;
    }
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->toHtml();
    }
    /**
     * @return array
     */
    public function getModuleList()
    {
        return $this->moduleListProcessor->getModuleList();
    }
}