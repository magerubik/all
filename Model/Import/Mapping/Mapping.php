<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */


namespace Magerubik\All\Model\Import\Mapping;

class Mapping implements MappingInterface
{
    /**
     * @var string
     */
    protected $masterAttributeCode = '';

    /**
     * FYI: column names pattern [a-z][a-z0-9_]*
     * @var array
     */
    protected $mappings = [
        /**
         * csv_column_name => model_column_name
         * model_column_name (numeric key means model_column_name => model_column_name)
        **/
    ];

    /**
     * @var array
     */
    private $processedMapping;

    /**
     * @inheritdoc
     */
    public function getValidColumnNames()
    {
        return array_keys($this->processedMapping());
    }

    /**
     * @inheritdoc
     */
    public function getMappedField($columnName)
    {
        if (!isset($this->processedMapping()[$columnName])) {
            throw new \Magerubik\All\Exceptions\MappingColumnDoesntExist();
        }

        return $this->processedMapping()[$columnName];
    }

    /**
     * @inheritdoc
     */
    public function getMasterAttributeCode()
    {
        if (empty($this->masterAttributeCode)) {
            throw new \Magerubik\All\Exceptions\MasterAttributeCodeDoesntSet();
        }

        return $this->masterAttributeCode;
    }

    public function processedMapping()
    {
        if (null === $this->processedMapping) {
            $this->processedMapping = [];
            foreach ($this->mappings as $csvField => $field) {
                if (is_numeric($csvField)) {
                    $this->processedMapping[$field] = $field;
                } else {
                    $this->processedMapping[$csvField] = $field;
                }
            }
        }

        return $this->processedMapping;
    }
}
