/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_Shopbybrand
 */
 
define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';
    return function (data) {
        $.widget('mage.menu', data.menu, {
            _create: function () {
                $(this.element).data('ui-menu', this);
                this._super();
            }
        });
        data.menu = $.mage.menu;
        return data;
    };
});