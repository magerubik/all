<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */


namespace Magerubik\All\Model\Import\Validation;

use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;

class EncodingValidator extends Validator implements ValidatorInterface
{
    const ENCODING_ERROR = 'encodingError';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::ENCODING_ERROR => '<b>Error!</b> Row has characters with Wrong Encoding'
    ];

    /**
     * @inheritdoc
     */
    public function validateRow(array $rowData, $behavior)
    {
        $this->errors = [];
        foreach ($rowData as $value) {
            if (!mb_check_encoding($value, 'UTF-8')) {
                $this->errors[self::ENCODING_ERROR] = ProcessingError::ERROR_LEVEL_CRITICAL;
                break;
            }
        }

        return parent::validateResult();
    }
}
