<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */


namespace Magerubik\All\Model\Import\Validation;

interface ValidatorPoolInterface
{
    /**
     * @return \Magerubik\All\Model\Import\Validation\ValidatorInterface[]
     */
    public function getValidators();

    /**
     * @param \Magerubik\All\Model\Import\Validation\ValidatorInterface
     *
     * @return void
     */
    public function addValidator($validator);
}
