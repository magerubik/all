<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */


namespace Magerubik\All\Model\Import\Mapping;

/**
 * @since 1.4.6
 */
interface MappingInterface
{
    /**
     * @return array
     */
    public function getValidColumnNames();

    /**
     * @param string $columnName
     *
     * @throws \Magerubik\All\Exceptions\MappingColumnDoesntExist
     * @return string|bool
     */
    public function getMappedField($columnName);

    /**
     * @throws \Magerubik\All\Exceptions\MasterAttributeCodeDoesntSet
     * @return string
     */
    public function getMasterAttributeCode();
}
