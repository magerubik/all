/**
 * Copyright © 2021 magerubik.com. All rights reserved.
 * @author Magerubik Team <info@magerubik.com>
 * @package Magerubik_All
 */
var config = {
	paths: {
		"mr_jvectormap": 'Magerubik_All/js/plugin/jquery-jvectormap',
		"mr_jvectormapWorld": 'Magerubik_All/js/plugin/jquery-jvectormap-world-mill-en'
    },
	shim: {
		'mr_jvectormap' : {
			'deps': ['jquery']
		},
		'mr_jvectormapWorld' : {
			'deps': ['jquery','mr_jvectormap']
		}		
	}
};
