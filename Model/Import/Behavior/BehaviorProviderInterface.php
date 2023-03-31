<?php
/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */


namespace Magerubik\All\Model\Import\Behavior;

interface BehaviorProviderInterface
{
    /**
     * @param string $behaviorCode
     *
     * @throws \Magerubik\All\Exceptions\NonExistentImportBehavior
     * @return \Magerubik\All\Model\Import\Behavior\BehaviorInterface
     */
    public function getBehavior($behaviorCode);
}
