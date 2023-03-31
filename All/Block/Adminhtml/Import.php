<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */


namespace Magerubik\All\Block\Adminhtml;

use Magento\Backend\Block\Template;

class Import extends Template
{
    /**
     * @var string
     */
    private $importEntityTypeCode;

    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        if (empty($data['entityTypeCode'])) {
            throw new \Magerubik\All\Exceptions\EntityTypeCodeNotSet();
        }
        $this->importEntityTypeCode = $data['entityTypeCode'];
        parent::__construct($context, $data);
    }

    public function getImportEntity()
    {
        return $this->importEntityTypeCode;
    }
}
