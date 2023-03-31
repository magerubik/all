/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
var config = {
    config: {
        mixins: {
            'mage/menu': {
                'Magerubik_All/js/lib/mage/menu-mixin': true
            }
        }
    },
	map: {
        '*': {
			"mr_frontend": 'Magerubik_All/js/mr_frontend',
			"mr_owlslider": 'Magerubik_All/owl-carousel/owl.carousel.min'
        }
    },
	shim:{
		"mr_owlslider": ["jquery"]
	}
};
